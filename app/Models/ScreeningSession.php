<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreeningSession extends Model
{
    protected $fillable = [
        'user_id',
        'screening_identity_id',
        'disease',
        'answers',
        'summary',
        'risk_level',
        'is_emergency',
    ];

    public function diseaseLabel(): ?string
    {
        if (! $this->disease) {
            return null;
        }

        return config("diseases.{$this->disease}.label");
    }

    /**
     * Level risiko untuk tampilan (TB Paru lama bisa tersimpan sebagai emergency → ditampilkan Tinggi).
     */
    public function displayRiskLevel(): string
    {
        $level = $this->risk_level;

        if ($this->disease === 'tb_paru' && $level === 'emergency') {
            return 'high';
        }

        return $level;
    }

    public function displayRiskLabel(): string
    {
        return match ($this->displayRiskLevel()) {
            'high' => 'Tinggi',
            'medium' => 'Sedang',
            'low' => 'Rendah',
            'emergency' => 'Darurat',
            default => $this->risk_level,
        };
    }

    public function showsEmergencyUi(): bool
    {
        if ($this->disease === 'tb_paru') {
            return false;
        }

        return $this->is_emergency || $this->risk_level === 'emergency';
    }

    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'is_emergency' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function identity(): BelongsTo
    {
        return $this->belongsTo(ScreeningIdentity::class, 'screening_identity_id');
    }
}
