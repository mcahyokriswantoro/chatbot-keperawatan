<?php

namespace App\Support;

class PhoneNumber
{
    public static function normalize(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone) ?? '';

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    public static function isValid(string $phone): bool
    {
        $normalized = self::normalize($phone);

        return (bool) preg_match('/^08[0-9]{8,11}$/', $normalized);
    }
}
