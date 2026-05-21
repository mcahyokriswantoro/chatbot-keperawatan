<?php

namespace App\Models;

use App\Enums\WilayahLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';

    protected $primaryKey = 'kode';

    public $incrementing = false;

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kode',
        'nama',
    ];

    public static function parentKodeFromKode(string $kode): ?string
    {
        $segments = explode('.', $kode);

        if (count($segments) <= 1) {
            return null;
        }

        array_pop($segments);

        return implode('.', $segments);
    }

    public static function segmentCount(string $kode): int
    {
        return count(explode('.', $kode));
    }

    public static function dotCount(string $kode): int
    {
        return substr_count($kode, '.');
    }

    public function getLevelAttribute(): WilayahLevel
    {
        return WilayahLevel::fromKode($this->kode);
    }

    public function getParentKodeAttribute(): ?string
    {
        return static::parentKodeFromKode($this->kode);
    }

    public function isProvinsi(): bool
    {
        return $this->level === WilayahLevel::Provinsi;
    }

    public function isKabupaten(): bool
    {
        return $this->level === WilayahLevel::Kabupaten;
    }

    public function isKecamatan(): bool
    {
        return $this->level === WilayahLevel::Kecamatan;
    }

    public function isDesa(): bool
    {
        return $this->level === WilayahLevel::Desa;
    }

    public function parent(): ?self
    {
        $parentKode = $this->parent_kode;

        return $parentKode ? static::find($parentKode) : null;
    }

    public function children(): Builder
    {
        return static::childrenOf($this->kode);
    }

    public function scopeProvinsi(Builder $query): Builder
    {
        return $query->whereRaw('kode NOT LIKE ?', ['%.%']);
    }

    public function scopeKabupaten(Builder $query): Builder
    {
        return $query->whereRaw('(LENGTH(kode) - LENGTH(REPLACE(kode, ".", ""))) = 1');
    }

    public function scopeKecamatan(Builder $query): Builder
    {
        return $query->whereRaw('(LENGTH(kode) - LENGTH(REPLACE(kode, ".", ""))) = 2');
    }

    public function scopeDesa(Builder $query): Builder
    {
        return $query->whereRaw('(LENGTH(kode) - LENGTH(REPLACE(kode, ".", ""))) = 3');
    }

    public function scopeChildrenOf(Builder $query, string $parentKode): Builder
    {
        $childDots = static::dotCount($parentKode) + 1;

        return $query
            ->where('kode', 'like', $parentKode.'.%')
            ->whereRaw('(LENGTH(kode) - LENGTH(REPLACE(kode, ".", ""))) = ?', [$childDots]);
    }

    public function scopeByLevel(Builder $query, WilayahLevel $level): Builder
    {
        return match ($level) {
            WilayahLevel::Provinsi => $query->provinsi(),
            WilayahLevel::Kabupaten => $query->kabupaten(),
            WilayahLevel::Kecamatan => $query->kecamatan(),
            WilayahLevel::Desa => $query->desa(),
        };
    }
}
