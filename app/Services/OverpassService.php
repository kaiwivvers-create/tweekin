<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Finds real hospitals, clinics and specialist facilities near a coordinate
 * using the OpenStreetMap Overpass API. No API key required.
 *
 * Runs server-side so we can set a proper User-Agent (Overpass rejects requests
 * without one), fall back across mirrors, and cache results.
 */
class OverpassService
{
    protected array $mirrors = [
        'https://overpass-api.de/api/interpreter',
        'https://overpass.kumi.systems/api/interpreter',
        'https://overpass.private.coffee/api/interpreter',
    ];

    protected int $radiusMetres = 20000;

    /**
     * @return array<int, array{name:string,type:string,address:string,phone:string,website:string,wikipedia:string,wikidata:string,lat:float,lon:float,distanceKm:float}>
     */
    public function findNearby(float $lat, float $lon, string $type = 'general'): array
    {
        $key = 'overpass:v3:' . $type . ':' . round($lat, 2) . ':' . round($lon, 2);

        return Cache::remember($key, now()->addHours(6), function () use ($lat, $lon, $type) {
            $raw = $this->query($lat, $lon, $type);
            if ($raw === null) {
                return [];
            }

            return $this->normalise($raw, $lat, $lon, $type);
        });
    }

    /**
     * Run the query against each mirror until one answers.
     */
    protected function query(float $lat, float $lon, string $type): ?array
    {
        $query = $this->buildQuery($lat, $lon, $type);

        foreach ($this->mirrors as $mirror) {
            try {
                $response = Http::withHeaders([
                    'User-Agent' => 'TweekScreeningApp/1.0 (symptom screening tool; contact: admin@tweek.app)',
                ])->asForm()->timeout(35)->post($mirror, ['data' => $query]);

                if ($response->successful()) {
                    $json = $response->json();

                    return is_array($json) ? $json : null;
                }

                Log::warning('Overpass mirror failed', [
                    'mirror' => $mirror,
                    'status' => $response->status(),
                ]);
            } catch (\Throwable $e) {
                Log::warning('Overpass mirror exception', [
                    'mirror' => $mirror,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    /**
     * Overpass QL. Specialist matches are queried alongside general facilities so
     * that facilities treating what the user described are in the candidate pool
     * at all; the pool itself is then ordered by distance.
     */
    protected function buildQuery(float $lat, float $lon, string $type): string
    {
        $at = '(around:' . $this->radiusMetres . ',' . $lat . ',' . $lon . ')';

        $parts = [
            'nwr["amenity"="hospital"]' . $at . ';',
            'nwr["healthcare"="hospital"]' . $at . ';',
            'nwr["amenity"="clinic"]' . $at . ';',
            'nwr["healthcare"="clinic"]' . $at . ';',
        ];

        if ($type === 'mental') {
            $parts[] = 'nwr["healthcare"="psychotherapist"]' . $at . ';';
            $parts[] = 'nwr["healthcare:speciality"~"psychiatry|psychotherapy|psychology|mental",i]' . $at . ';';
        } else {
            $parts[] = 'nwr["healthcare"="centre"]' . $at . ';';
            $parts[] = 'nwr["amenity"="doctors"]' . $at . ';';
        }

        return "[out:json][timeout:30];\n(\n" . implode("\n", $parts) . "\n);\nout center tags 100;";
    }

    /**
     * Turn raw OSM elements into a ranked, de-duplicated list.
     */
    protected function normalise(array $raw, float $lat, float $lon, string $type): array
    {
        $seen = [];
        $places = [];

        foreach ($raw['elements'] ?? [] as $element) {
            $tags = $element['tags'] ?? [];

            $name = trim((string) ($tags['name'] ?? $tags['name:en'] ?? $tags['operator'] ?? ''));
            if ($name === '') {
                continue;
            }

            // Skip facilities that are closed, demolished, or not built yet.
            foreach (['disused', 'abandoned', 'construction', 'proposed', 'demolished'] as $flag) {
                if (!empty($tags[$flag]) || ($tags['building'] ?? '') === $flag) {
                    continue 2;
                }
            }

            $placeLat = $element['lat'] ?? ($element['center']['lat'] ?? null);
            $placeLon = $element['lon'] ?? ($element['center']['lon'] ?? null);
            if ($placeLat === null || $placeLon === null) {
                continue;
            }

            $key = mb_strtolower($name);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;

            $speciality = (string) ($tags['healthcare:speciality'] ?? $tags['speciality'] ?? '');
            $firstSpeciality = trim(explode(';', $speciality)[0] ?? '');

            $relevance = 0;
            if ($firstSpeciality !== '' && preg_match('/psych|mental|anxiet|eating|nutrition|sleep|respirat|cardio|gastro|derma|ortho|neuro/i', $firstSpeciality)) {
                $relevance += 2;
            }
            if (($tags['healthcare'] ?? '') === 'psychotherapist') {
                $relevance += 2;
            }
            if (($tags['amenity'] ?? '') === 'hospital' || ($tags['healthcare'] ?? '') === 'hospital') {
                $relevance += 1;
            }

            $address = implode(', ', array_filter([
                trim(($tags['addr:housenumber'] ?? '') . ' ' . ($tags['addr:street'] ?? '')),
                $tags['addr:city'] ?? $tags['addr:town'] ?? $tags['addr:suburb'] ?? '',
            ]));

            $places[] = [
                'name' => $name,
                'type' => $firstSpeciality !== ''
                    ? $this->titleCase($firstSpeciality)
                    : $this->titleCase((string) ($tags['healthcare'] ?? $tags['amenity'] ?? 'Health facility')),
                'address' => $address,
                'phone' => trim((string) ($tags['phone'] ?? $tags['contact:phone'] ?? '')),
                'website' => trim((string) ($tags['website'] ?? $tags['contact:website'] ?? '')),
                // OSM often links the facility's Wikipedia/Wikidata entry directly,
                // which gives us its real photo with no guesswork.
                'wikipedia' => trim((string) ($tags['wikipedia'] ?? $tags['wikipedia:en'] ?? '')),
                'wikidata' => trim((string) ($tags['wikidata'] ?? '')),
                'lat' => (float) $placeLat,
                'lon' => (float) $placeLon,
                'distanceKm' => round($this->haversineKm($lat, $lon, (float) $placeLat, (float) $placeLon), 1),
                '_relevance' => $relevance,
            ];
        }

        // Nearest first. The model re-ranks this pool by fit for the user's specific
        // concern afterwards, so the pool itself should hold the closest real
        // facilities rather than the most specialised ones 19 km away.
        usort($places, function ($a, $b) {
            if ($a['distanceKm'] !== $b['distanceKm']) {
                return $a['distanceKm'] <=> $b['distanceKm'];
            }

            return $b['_relevance'] <=> $a['_relevance'];
        });

        $places = array_slice($places, 0, 12);

        return array_map(function ($place) {
            unset($place['_relevance']);

            return $place;
        }, $places);
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

    protected function titleCase(string $value): string
    {
        return ucwords(trim(preg_replace('/[-_]+/', ' ', $value) ?? $value));
    }
}
