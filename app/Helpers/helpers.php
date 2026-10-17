<?php

if (!function_exists('company')) {
    function company(string $key, $default = null)
    {
        return config('company.' . $key, $default);
    }
}

if (!function_exists('rupiah')) {
    function rupiah($value)
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}

if (!function_exists('angka')) {
    /**
     * Format angka/jumlah tanpa nol di belakang. Contoh: 0.2500 -> "0,25", 50 -> "50".
     */
    function angka($value, int $precision = 4): string
    {
        $formatted = number_format((float) $value, $precision, ',', '.');

        if (str_contains($formatted, ',')) {
            $formatted = rtrim(rtrim($formatted, '0'), ',');
        }

        return $formatted;
    }
}
