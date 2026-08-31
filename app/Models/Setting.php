<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'json' => json_decode($setting->value, true),
            'integer' => (int) $setting->value,
            default => $setting->value,
        };
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, mixed $value, string $type = 'string'): static
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type' => $type,
            ]
        );
    }

    /**
     * Get all settings as a key => value array
     */
    public static function allAsArray(): array
    {
        return static::pluck('value', 'key')->mapWithKeys(function ($value, $key) {
            $setting = static::where('key', $key)->first();
            return [$key => match ($setting->type) {
                'boolean' => (bool) $value,
                'json' => json_decode($value, true),
                'integer' => (int) $value,
                default => $value,
            }];
        })->toArray();
    }
}
