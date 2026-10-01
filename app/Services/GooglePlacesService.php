<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Google Places API (New) lookup for real nearby care facilities.
 *
 * Gives real names, addresses, phone numbers, ratings and — importantly — a
 * photo for essentially every place, which the keyless Wikimedia sources cannot.
 *
 * Requires a Google Cloud key with "Places API (New)" enabled and billing on.
 */
class GooglePlacesService
{
    protected string $endpoint = 'https://places.googleapis.com/v1';

    protected string $apiKey;

    /** Fields worth paying for: identity, location, photo, and a number to call. */
    protected string $fieldMask = 'places.displayName,places.formattedAddress,'
        . 'places.location,places.primaryTypeDisplayName,places.photos,'
        . 'places.nationalPhoneNumber,places.internationalPhoneNumber,'
        . 'places.googleMapsUri,places.rating,places.userRatingCount';

    public function __construct()
    {
        $this->apiKey = (string) Setting::get(
            'google_places_api_key',
            env('GOOGLE_PLACES_API_KEY', '')
        );
    }

    public function isConfigured(): bool
    {
        return trim($this->apiKey) !== '';
    }

    /**
     * Real care facilities near a coordinate.
     *
     * @return array<int, array<string, mixed>>
     */
    public function searchNearby(float $lat, float $lon, string $type = 'general', int $radius = 20000): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        $key = 'places:v1:' . $type . ':' . round($lat, 2) . ':' . round($lon, 2);

        return Cache::remember($key, now()->addHours(6), function () use ($lat, $lon, $type, $radius) {
            $response = $type === 'mental'
                ? $this->textSearch($lat, $lon, $radius)
                : $this->nearbySearch($lat, $lon, $radius);

            if ($response === null) {
                return [];
            }

            return $this->normalise($response, $lat, $lon);
        });
    }

    /**
     * Physical concerns: hospitals, clinics and doctors.
     */
    protected function nearbySearch(float $lat, float $lon, int $radius): ?array
    {
        return $this->post('places:searchNearby', [
            'includedTypes' => ['hospital', 'doctor', 'medical_clinic', 'physiotherapist'],
            'maxResultCount' => 20,
            'rankPreference' => 'DISTANCE',
            'locationRestriction' => [
                'circle' => [
                    'center' => ['latitude' => $lat, 'longitude' => $lon],
                    'radius' => (float) $radius,
                ],
            ],
        ]);
    }

    /**
     * Mental health has no matching place type, so a text search targeted at
     * psychiatry/therapy finds far better matches than a generic hospital list.
     */
    protected function textSearch(float $lat, float $lon, int $radius): ?array
    {
        return $this->post('places:searchText', [
            'textQuery' => 'mental health clinic psychiatrist psychologist therapist counseling',
            'maxResultCount' => 20,
            'locationBias' => [
                'circle' => [
                    'center' => ['latitude' => $lat, 'longitude' => $lon],
                    'radius' => (float) $radius,
                ],
            ],
        ]);
    }

    protected function post(string $path, array $body): ?array
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-Goog-Api-Key' => $this->apiKey,
                'X-Goog-FieldMask' => $this->fieldMask,
            ])->timeout(20)->post("{$this->endpoint}/{$path}", $body);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('Google Places error', [
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error('Google Places exception', ['path' => $path, 'message' => $e->getMessage()]);

            return null;
        }
    }

    /**
     * Shape Google's response into the same structure OverpassService returns.
     */
    protected function normalise(array $response, float $lat, float $lon): array
    {
        $places = [];

        foreach ($response['places'] ?? [] as $place) {
            $name = trim((string) ($place['displayName']['text'] ?? ''));
            if ($name === '') {
                continue;
            }

            $placeLat = $place['location']['latitude'] ?? null;
            $placeLon = $place['location']['longitude'] ?? null;
            if ($placeLat === null || $placeLon === null) {
                continue;
            }

            $photoName = $place['photos'][0]['name'] ?? null;

            $places[] = [
                'name' => $name,
                'type' => trim((string) ($place['primaryTypeDisplayName']['text'] ?? '')) ?: 'Health facility',
                'address' => trim((string) ($place['formattedAddress'] ?? '')),
                'phone' => trim((string) ($place['nationalPhoneNumber']
                    ?? $place['internationalPhoneNumber']
                    ?? '')),
                'website' => '',
                'mapsUrl' => trim((string) ($place['googleMapsUri'] ?? '')),
                'rating' => isset($place['rating']) ? round((float) $place['rating'], 1) : null,
                'ratingCount' => isset($place['userRatingCount']) ? (int) $place['userRatingCount'] : null,
                'photoRef' => $photoName,
                'wikipedia' => '',
                'wikidata' => '',
                'lat' => (float) $placeLat,
                'lon' => (float) $placeLon,
                'distanceKm' => round($this->haversineKm($lat, $lon, (float) $placeLat, (float) $placeLon), 1),
            ];
        }

        usort($places, fn($a, $b) => $a['distanceKm'] <=> $b['distanceKm']);

        return array_slice($places, 0, 12);
    }

    /**
     * Google's photo endpoint redirects to the image. We resolve that final URL
     * server-side so the API key never reaches the browser.
     */
    public function resolvePhotoUrl(string $photoName, int $maxWidth = 900): ?string
    {
        if (!$this->isConfigured() || $photoName === '') {
            return null;
        }

        $key = 'place-photo:' . md5($photoName . ':' . $maxWidth);

        return Cache::remember($key, now()->addHours(12), function () use ($photoName, $maxWidth) {
            try {
                $response = Http::timeout(10)->get(
                    "{$this->endpoint}/{$photoName}/media",
                    [
                        'maxWidthPx' => $maxWidth,
                        'skipHttpRedirect' => 'true',
                        'key' => $this->apiKey,
                    ]
                );

                if ($response->successful()) {
                    $uri = $response->json('photoUri');

                    return is_string($uri) && $uri !== '' ? $uri : null;
                }

                Log::warning('Google Places photo error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return null;
            } catch (\Throwable $e) {
                Log::error('Google Places photo exception', ['message' => $e->getMessage()]);

                return null;
            }
        });
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
}
