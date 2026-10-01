<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Resolves a photo for a facility from free, keyless sources.
 *
 * This deliberately errs on the side of showing nothing. A wrong building on a
 * card that says "care near you" is worse than a clean placeholder, so a photo is
 * only returned when the source ties it to that facility by name or by an
 * explicit Wikipedia/Wikidata reference coming from OpenStreetMap.
 */
class PlaceImageService
{
    /** Name particles that match half the buildings in any city. */
    protected array $particles = [
        'st', 'saint', 'santa', 'san', 'santo', 'la', 'las', 'los', 'el',
        'de', 'del', 'da', 'do', 'di', 'van', 'von', 'bin', 'binti', 'al',
        'the', 'of', 'and', 'a', 'an', 'at', 'in', 'on', 'for', 'to',
    ];

    /** Filenames that are diagrams rather than photographs. */
    protected string $diagramPattern = '/\bmap\b|\bdiagram\b|\bschematic\b|\bplan\b|\bchart\b|\bflag\b|coat[_ ]of[_ ]arms|\.svg/i';

    /** Filenames that sit in a hospital's Commons category but are not the building. */
    protected string $nonPhotoPattern = '/marker|plaque|monument|memorial|portrait|headshot|logo|poster|screenshot|vaccin|vial|protest|rally|banner|seal\b|mascot|statue|map_of|panorama_annotated/i';

    public function resolve(
        string $name,
        ?float $lat = null,
        ?float $lon = null,
        ?string $hint = null,
        ?string $wikipedia = null,
        ?string $wikidata = null
    ): ?string {
        $name = trim($name);
        if ($name === '') {
            return null;
        }

        $key = 'place-image:v6:' . md5(implode('|', [
            mb_strtolower($name),
            mb_strtolower((string) $hint),
            mb_strtolower((string) $wikipedia),
            mb_strtolower((string) $wikidata),
        ]));

        // A resolved photo is stable, so it caches for a week. A miss may just be a
        // transient API failure, so it only sticks around briefly.
        $cached = Cache::get($key);
        if ($cached !== null) {
            return $cached === '' ? null : $cached;
        }

        $url = $this->fromOsmReference($wikipedia, $wikidata)
            ?? $this->fromExactArticle($name, $hint, $lat, $lon)
            ?? $this->fromCommonsCategory($name)
            ?? $this->fromCommons($name, $lat, $lon);

        Cache::put($key, $url ?? '', $url ? now()->addDays(7) : now()->addMinutes(20));

        return $url;
    }

    /**
     * Follow the Wikipedia/Wikidata link OSM gives us. This is authoritative, so
     * the image is trusted even if it turns out to be the facility's crest.
     */
    protected function fromOsmReference(?string $wikipedia, ?string $wikidata): ?string
    {
        if (!empty($wikipedia)) {
            $title = preg_replace('/^[a-z]{2,3}:/i', '', trim($wikipedia)) ?? trim($wikipedia);
            $url = $this->pageImageByTitle($title);
            if ($url !== null) {
                return $url;
            }
        }

        if (!empty($wikidata) && preg_match('/^Q\d+$/', trim($wikidata))) {
            $file = $this->wikidataImageFile(trim($wikidata));
            if ($file !== null) {
                return 'https://commons.wikimedia.org/wiki/Special:FilePath/'
                    . rawurlencode(str_replace(' ', '_', $file)) . '?width=800';
            }
        }

        return null;
    }

    /**
     * Page image for an exact article title.
     */
    protected function pageImageByTitle(string $title): ?string
    {
        if ($title === '') {
            return null;
        }

        try {
            $response = Http::timeout(8)->withHeaders([
                'User-Agent' => 'TweekScreeningApp/1.0 (symptom screening tool)',
            ])->get('https://en.wikipedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'titles' => $title,
                'redirects' => 1,
                'prop' => 'pageimages',
                'piprop' => 'thumbnail|original',
                'pithumbsize' => 800,
            ]);

            if (!$response->successful()) {
                return null;
            }

            foreach ($response->json('query.pages') ?? [] as $page) {
                $image = $page['thumbnail']['source'] ?? ($page['original']['source'] ?? null);
                if ($image && !preg_match($this->diagramPattern, $image)) {
                    return $image;
                }
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Wikidata P18 (image) claim for an entity.
     */
    protected function wikidataImageFile(string $entity): ?string
    {
        try {
            $response = Http::timeout(8)->withHeaders([
                'User-Agent' => 'TweekScreeningApp/1.0 (symptom screening tool)',
            ])->get('https://www.wikidata.org/w/api.php', [
                'action' => 'wbgetclaims',
                'format' => 'json',
                'entity' => $entity,
                'property' => 'P18',
            ]);

            if (!$response->successful()) {
                return null;
            }

            $value = $response->json('claims.P18.0.mainsnak.datavalue.value');

            return is_string($value) && $value !== '' ? $value : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Search Wikipedia for the facility and only accept an article whose title
     * contains the facility name as a whole run of words.
     */
    protected function fromExactArticle(string $name, ?string $hint, ?float $lat, ?float $lon): ?string
    {
        $canVerifyLocality = $lat !== null && $lon !== null;

        foreach (array_filter(array_unique([$name, trim($name . ' ' . (string) $hint)])) as $query) {
            try {
                $response = Http::timeout(8)->withHeaders([
                    'User-Agent' => 'TweekScreeningApp/1.0 (symptom screening tool)',
                ])->get('https://en.wikipedia.org/w/api.php', [
                    'action' => 'query',
                    'format' => 'json',
                    'generator' => 'search',
                    'gsrsearch' => $query,
                    'gsrlimit' => 5,
                    'gsrnamespace' => 0,
                    'prop' => 'pageimages|coordinates',
                    'piprop' => 'thumbnail',
                    'pithumbsize' => 800,
                    'coprop' => 'type',
                    'colimit' => 5,
                ]);

                if (!$response->successful()) {
                    continue;
                }

                foreach ($response->json('query.pages') ?? [] as $page) {
                    $title = (string) ($page['title'] ?? '');
                    $image = $page['thumbnail']['source'] ?? null;

                    if (!$title || !$image || preg_match($this->diagramPattern, $image)) {
                        continue;
                    }
                    if (!$this->titleContainsName($title, $name)) {
                        continue;
                    }

                    $articleLat = $page['coordinates'][0]['lat'] ?? null;
                    $articleLon = $page['coordinates'][0]['lon'] ?? null;

                    // Same name is not enough — "St. Luke's Medical Center" also
                    // names a hospital in Texas. Confirm it is the local one.
                    if ($articleLat === null || $articleLon === null) {
                        continue;
                    }

                    if ($canVerifyLocality
                        && $this->haversineKm($lat, $lon, (float) $articleLat, (float) $articleLon) > 25.0) {
                        continue;
                    }

                    return $image;
                }
            } catch (\Throwable $e) {
                continue;
            }
        }

        return null;
    }

    /**
     * Commons category named after the facility.
     *
     * Hospitals with any Commons coverage almost always have a category, and the
     * building photo inside it is named after the hospital — "Entrance to the
     * Philippine General Hospital". The category itself is not trusted though:
     * only files whose own name carries the facility name qualify, so a
     * vaccination drive or a protest shot that happens to live in the category
     * can never be shown.
     */
    protected function fromCommonsCategory(string $name): ?string
    {
        try {
            $response = Http::timeout(8)->withHeaders([
                'User-Agent' => 'TweekScreeningApp/1.0 (symptom screening tool)',
            ])->get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'list' => 'search',
                'srsearch' => $name,
                'srnamespace' => 14,
                'srlimit' => 5,
            ]);

            if (!$response->successful()) {
                return null;
            }

            foreach ($response->json('query.search') ?? [] as $match) {
                $category = (string) ($match['title'] ?? '');
                if ($category === '') {
                    continue;
                }

                $plain = preg_replace('/^category:/i', '', $category) ?? $category;

                // The category has to be this facility, not the city it sits in.
                if (!$this->titleContainsName($plain, $name)) {
                    continue;
                }

                $photo = $this->mostOnTopicFile($category, $name);
                if ($photo !== null) {
                    return $photo;
                }
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * The least incidental photo in a category: among files whose name carries
     * the facility name, the one with the fewest extra words — "PGH Nurses Home"
     * over "09117jfUnited Nations Avenue Ermita Manila Doctorsfvf 04".
     */
    protected function mostOnTopicFile(string $category, string $name): ?string
    {
        try {
            $response = Http::timeout(8)->withHeaders([
                'User-Agent' => 'TweekScreeningApp/1.0 (symptom screening tool)',
            ])->get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'generator' => 'categorymembers',
                'gcmtitle' => $category,
                'gcmtype' => 'file',
                'gcmlimit' => 30,
                'prop' => 'imageinfo',
                'iiprop' => 'url|size|mime',
                'iiurlwidth' => 800,
            ]);

            if (!$response->successful()) {
                return null;
            }

            $best = null;
            $bestLength = PHP_INT_MAX;

            foreach ($response->json('query.pages') ?? [] as $page) {
                $info = $page['imageinfo'][0] ?? null;
                if (!$info) {
                    continue;
                }

                $title = (string) ($page['title'] ?? '');
                $url = $info['thumburl'] ?? $info['url'] ?? null;

                if (!$url || ($info['mime'] ?? '') === 'image/svg+xml') {
                    continue;
                }
                if ((int) ($info['width'] ?? 0) < 400) {
                    continue;
                }
                if (preg_match($this->diagramPattern, $url) || preg_match($this->nonPhotoPattern, $url)) {
                    continue;
                }
                if (!$this->titleContainsName($title, $name)) {
                    continue;
                }

                $length = count($this->matchTokens($title));
                if ($length < $bestLength) {
                    $best = $url;
                    $bestLength = $length;
                }
            }

            return $best;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Commons photos taken at the facility, only when the file name also carries
     * the facility's name.
     */
    protected function fromCommons(string $name, ?float $lat, ?float $lon): ?string
    {
        if ($lat === null || $lon === null) {
            return null;
        }

        try {
            $response = Http::timeout(8)->withHeaders([
                'User-Agent' => 'TweekScreeningApp/1.0 (symptom screening tool)',
            ])->get('https://commons.wikimedia.org/w/api.php', [
                'action' => 'query',
                'format' => 'json',
                'generator' => 'geosearch',
                'ggscoord' => $lat . '|' . $lon,
                'ggsradius' => 400,
                'ggslimit' => 15,
                'ggsnamespace' => 6,
                'prop' => 'imageinfo',
                'iiprop' => 'url|size|mime',
                'iiurlwidth' => 800,
            ]);

            if (!$response->successful()) {
                return null;
            }

            foreach ($response->json('query.pages') ?? [] as $page) {
                $info = $page['imageinfo'][0] ?? null;
                if (!$info) {
                    continue;
                }

                $url = $info['thumburl'] ?? $info['url'] ?? null;
                if (!$url || ($info['mime'] ?? '') === 'image/svg+xml') {
                    continue;
                }
                if ((int) ($info['width'] ?? 0) < 400 || preg_match($this->diagramPattern, $url)) {
                    continue;
                }
                if (!$this->titleContainsName((string) ($page['title'] ?? ''), $name)) {
                    continue;
                }

                return $url;
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Does the candidate text contain the facility name as a contiguous run of
     * words? "Makati Medical Center (Makati; 03-21-2021)" contains
     * "Makati Medical Center"; "Ayala Avenue skyscrapers" does not.
     */
    protected function titleContainsName(string $candidate, string $facilityName): bool
    {
        $needle = $this->matchTokens($facilityName);
        if (count($needle) === 0) {
            return false;
        }

        $haystack = $this->matchTokens($candidate);
        if (count($haystack) < count($needle)) {
            return false;
        }

        $length = count($needle);
        $limit = count($haystack) - $length;

        for ($i = 0; $i <= $limit; $i++) {
            if (array_slice($haystack, $i, $length) === $needle) {
                return true;
            }
        }

        return false;
    }

    protected function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthRadius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }

    /**
     * Normalise to comparable word tokens, dropping only name particles.
     */
    protected function matchTokens(string $value): array
    {
        $value = mb_strtolower($value);
        $value = preg_replace('/[^a-z0-9\s]/', ' ', $value) ?? '';
        $words = preg_split('/\s+/', trim($value)) ?: [];

        return array_values(array_filter(
            $words,
            fn($w) => $w !== '' && !in_array($w, $this->particles, true)
        ));
    }
}
