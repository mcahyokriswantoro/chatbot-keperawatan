<?php

namespace App\Enums;

enum WilayahLevel: string
{
    case Provinsi = 'provinsi';
    case Kabupaten = 'kabupaten';
    case Kecamatan = 'kecamatan';
    case Desa = 'desa';

    public function label(): string
    {
        return match ($this) {
            self::Provinsi => 'Provinsi',
            self::Kabupaten => 'Kabupaten/Kota',
            self::Kecamatan => 'Kecamatan',
            self::Desa => 'Kelurahan/Desa',
        };
    }

    public static function fromKode(string $kode): self
    {
        $segmentCount = count(explode('.', $kode));

        return match ($segmentCount) {
            1 => self::Provinsi,
            2 => self::Kabupaten,
            3 => self::Kecamatan,
            4 => self::Desa,
            default => throw new \InvalidArgumentException("Kode wilayah tidak valid: {$kode}"),
        };
    }
}
