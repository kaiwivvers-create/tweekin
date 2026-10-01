<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Coarse, city-level location from the visitor's IP address.
 *
 * This is the fallback for the "Care near you" card when the browser refuses or
 * cannot provide GPS coordinates (permission denied, insecure origin, desktop
 * with location services off). It is deliberately approximate: everything it
 * finds is labelled as coming from the network so the UI never implies it is
 * the user's exact position.
 *
 * Both mirrors are keyless and return JSON, which matches how the rest of the
 * nearby-care stack (Overpass, Wikipedia) is wired — no account to configure.
 */
class IpLocationService
{
    /**
     * Keyless mirrors, tried in order until one answers with usable coordinates.
     *
     * @var array<string, array{url:string, shape:string}>
     */
    protected array $providers = [
        'ipwho.is' => ['url' => 'https://ipwho.is/{ip}', 'shape' => 'latlon-fields'],
        'ipinfo.io' => ['url' => 'https://ipinfo.io/{ip}/json', 'shape' => 'loc-pair'],
    ];

    /**
     * @return array{lat:float, lon:float, label:string}|null
     */
    public function locate(?string $ip): ?array
    {
        if ($ip === null || $ip === '' || !$this->isPublic($ip)) {
            return null;
        }

        return Cache::remember(
            'ip-location:v1:' . $ip,
            now()->addHours(24),
            fn () => $this->lookup($ip)
        );
    }

    /**
     * A private or reserved address (127.0.0.1, 192.168.x.x, 10.x.x.x, …) belongs
     * to the local machine or LAN, so asking a geolocation API about it would
     * only ever return the datacenter of the network the server sits in.
     */
    protected function isPublic(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) !== false;
    }

    /**
     * @return array{lat:float, lon:float, label:string}|null
     */
    protected function lookup(string $ip): ?array
    {
        foreach ($this->providers as $name => $provider) {
            try {
                $response = Http::timeout(6)
                    ->acceptJson()
                    ->get(str_replace('{ip}', $ip, $provider['url']));

                if (!$response->successful()) {
                    Log::warning('IP location mirror failed', [
                        'mirror' => $name,
                        'status' => $response->status(),
                    ]);

                    continue;
                }

                $located = $this->normalise($response->json(), $provider['shape']);

                if ($located !== null) {
                    return $located;
                }
            } catch (\Throwable $e) {
                Log::warning('IP location mirror exception', [
                    'mirror' => $name,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return null;
    }

    /**
     * @return array{lat:float, lon:float, label:string}|null
     */
    protected function normalise(mixed $data, string $shape): ?array
    {
        if (!is_array($data)) {
            return null;
        }

        // ipwho.is reports failures in the body with HTTP 200.
        if (array_key_exists('success', $data) && $data['success'] === false) {
            return null;
        }

        if ($shape === 'loc-pair') {
            $parts = explode(',', (string) ($data['loc'] ?? ''));

            if (count($parts) !== 2) {
                return null;
            }

            $lat = (float) $parts[0];
            $lon = (float) $parts[1];
        } else {
            if (!isset($data['latitude'], $data['longitude'])) {
                return null;
            }

            $lat = (float) $data['latitude'];
            $lon = (float) $data['longitude'];
        }

        if ($lat === 0.0 && $lon === 0.0) {
            return null;
        }

        $label = implode(', ', array_filter([
            trim((string) ($data['city'] ?? '')),
            trim((string) ($data['region'] ?? '')),
            trim((string) ($data['country'] ?? '')),
        ]));

        return [
            'lat' => $lat,
            'lon' => $lon,
            'label' => $label,
        ];
    }
}
