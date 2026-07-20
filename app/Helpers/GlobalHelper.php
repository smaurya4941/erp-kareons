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
