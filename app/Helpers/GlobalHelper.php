<?php

if (!function_exists('setting')) {
    /**
     * Get a global setting value from the database.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting(string $key, $default = null)
    {
        try {
            $setting = \App\Models\Setting::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        } catch (\Exception $e) {
            // Fallback in case table doesn't exist yet (e.g. during migrations)
            return $default;
        }
    }
}

if (!function_exists('company_name')) {
    /**
     * Get the configured company/brand name.
     */
    function company_name(): string
    {
        return setting('company_name', config('app.name', 'KareOns'));
    }
}

if (!function_exists('company_logo_url')) {
    /**
     * Resolve the brand logo URL.
     * Uses the admin-uploaded logo if set, otherwise the bundled default.
     */
    function company_logo_url(): string
    {
        $logo = setting('company_logo');

        try {
            if ($logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($logo)) {
                return asset('storage/' . $logo);
            }
        } catch (\Exception $e) {
            // ignore and fall through to default
        }

        return asset('images/logo.png');
    }
}

if (!function_exists('favicon_url')) {
    /**
     * Resolve the favicon URL.
     * Uses the admin-uploaded favicon, then the logo, then the bundled default.
     */
    function favicon_url(): string
    {
        $favicon = setting('favicon');

        try {
            if ($favicon && \Illuminate\Support\Facades\Storage::disk('public')->exists($favicon)) {
                return asset('storage/' . $favicon);
            }
        } catch (\Exception $e) {
            // ignore and fall through
        }

        return company_logo_url();
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Format a number into currency.
     *
     * @param float|int $amount
     * @param string $currency
     * @return string
     */
    function formatCurrency($amount, string $currency = '₹')
    {
        return $currency . ' ' . number_format((float)$amount, 2);
    }
}
