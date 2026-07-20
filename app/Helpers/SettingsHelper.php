<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key.
     * 
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        try {
            // Load all settings into cache forever to minimize DB queries
            $settings = Cache::rememberForever('app_settings', function () {
                // Ensure table exists before querying, to prevent errors during initial migrate/install
                if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                    return Setting::all()->keyBy('key');
                }
                return collect();
            });

            if ($settings->has($key)) {
                $setting = $settings->get($key);
                return $setting->casted_value;
            }

            return $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}
