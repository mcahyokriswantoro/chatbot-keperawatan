<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsultationVoucher extends Model
{
    protected $fillable = [
        'code',
        'discount_percent',
        'provider_key',
        'max_uses',
        'uses_count',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_percent' => 'integer',
            'max_uses' => 'integer',
            'uses_count' => 'integer',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ConsultationOrder::class);
    }

    public function isValidFor(string $providerKey): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->uses_count >= $this->max_uses) {
            return false;
        }

        if ($this->provider_key && $this->provider_key !== $providerKey) {
            return false;
        }

        return true;
    }

    public function coversFullPrice(): bool
    {
        return $this->discount_percent >= 100;
    }
}
