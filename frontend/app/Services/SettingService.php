<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SettingService
{
    protected $api;

    public function __construct()
    {
        $this->api = env('API_BASE_URL', 'http://localhost:8000');
    }

    /**
     * Get a setting's default value (based on is_default flag).
     */
    public function get($key, $default = null)
    {
        return Cache::remember("setting_{$key}", 60, function () use ($key, $default) {
            $response = Http::get("{$this->api}/settings");

            if ($response->failed()) {
                return $default;
            }

            $settings = $response->json();

            // Find the right setting by name
            $setting = collect($settings)->firstWhere('name', $key);

            if (!$setting || empty($setting['options'])) {
                return $default;
            }

            // Find the option marked as default
            $defaultOption = collect($setting['options'])->firstWhere('is_default', true);

            return $defaultOption['value'] ?? $default;
        });
    }

    /**
     * Clear cache for a setting.
     */
    public function clearCache($key)
    {
        Cache::forget("setting_{$key}");
    }
}
