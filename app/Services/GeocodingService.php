<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Turns a place the user typed ("Leeds", "90210", "Aizawl, Mizoram") into
 * coordinates we can search against.
 *
 * This exists because the coarse IP fallback cannot help everyone: a visitor on
 * localhost, a VPN, or a mobile carrier NAT can all resolve to a useless
 * location. Typing an area is the only path that always works, and it keeps the
 * "Care near you" card anchored to real places instead of invented ones.
 *
 * Uses the same keyless OpenStreetMap/Nominatim service the browser already
 * talks to for reverse geocoding. Results are cached for a day, which also keeps
 * us comfortably inside Nominatim's one-request-per-second usage policy.
 */
class GeocodingService
{
    protected string $endpoint = 'https://nominatim.openstreetmap.org/search';

    /**
     * @return array{lat:float, lon:float, label:string}|null
     */
    public function search(string $query): ?array
    {
        $query = trim($query);

        if (mb_strlen($query) < 2) {
            return null;
        }

        return Cache::remember(
            'geocode:v1:' . md5(mb_strtolower($query)),
            now()->addHours(24),
            fn () => $this->lookup($query)
        );
    }

    /**
     * @return array{lat:float, lon:float, label:string}|null
     */
    protected function lookup(string $query): ?array
    {
        try {
            $response = Http::timeout(8)
                ->withHeaders([
                    'User-Agent' => 'TweekScreeningApp/1.0 (symptom screening tool; contact: admin@tweek.app)',
                ])
                ->get($this->endpoint, [
                    'q' => $query,
                    'format' => 'jsonv2',
                    'limit' => 1,
                    'addressdetails' => 1,
                ]);

            if (!$response->successful()) {
                Log::warning('Geocoding lookup failed', ['status' => $response->status()]);

                return null;
            }

            $match = $response->json()[0] ?? null;

            if (!is_array($match) || !isset($match['lat'], $match['lon'])) {
                return null;
            }

            $address = $match['address'] ?? [];

            // Build a short, human label ("Leeds, England, United Kingdom") rather
            // than Nominatim's full display name, which can run to a whole line.
            $label = implode(', ', array_filter([
                $address['city'] ?? $address['town'] ?? $address['village'] ?? $address['municipality'] ?? $address['county'] ?? '',
                $address['state'] ?? '',
                $address['country'] ?? '',
            ]));

            return [
                'lat' => (float) $match['lat'],
                'lon' => (float) $match['lon'],
                'label' => $label !== '' ? $label : $query,
            ];
        } catch (\Throwable $e) {
            Log::warning('Geocoding lookup exception', ['message' => $e->getMessage()]);

            return null;
        }
    }
}
