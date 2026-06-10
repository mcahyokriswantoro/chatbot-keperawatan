<?php

namespace App\Support;

class GenderMapper
{
    public static function toScreening(?string $gender): string
    {
        return match ($gender) {
            'laki-laki', 'laki_laki' => 'laki_laki',
            'perempuan' => 'perempuan',
            default => '',
        };
    }

    public static function label(?string $gender): string
    {
        return match (self::toScreening($gender)) {
            'laki_laki' => 'Laki-laki',
            'perempuan' => 'Perempuan',
            default => '-',
        };
    }
}
