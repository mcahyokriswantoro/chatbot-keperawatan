<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScreeningIdentity extends Model
{
    protected $fillable = [
        'user_id',
        'screening_target',
        'disease',
        'name',
        'gender',
        'phone',
        'date_of_birth',
        'age',
        'weight_kg',
        'height_cm',
        'domicile_address',
        'occupation',
        'address',
        'province',
        'province_kode',
        'regency',
        'regency_kode',
        'district',
        'district_kode',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'weight_kg' => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function screeningSessions(): HasMany
    {
        return $this->hasMany(ScreeningSession::class);
    }

    public function diseaseLabel(): ?string
    {
        return config("diseases.{$this->disease}.label");
    }
}
