<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthMonitoring extends Model
{
    protected $fillable = [
        'user_id',
        'complaints',
        'medication_name',
        'medication_dose',
        'medication_schedule',
        'activities',
        'diet_compliant',
        'diet_notes',
        'systolic',
        'diastolic',
        'heart_rate',
        'temperature',
        'respiratory_rate',
        'blood_sugar',
        'oxygen_saturation',
        'weight',
        'notes',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'date',
            'diet_compliant' => 'boolean',
            'blood_sugar' => 'decimal:1',
            'weight' => 'decimal:1',
            'temperature' => 'decimal:1',
        ];
    }

    public function bloodPressureLabel(): ?string
    {
        if ($this->systolic && $this->diastolic) {
            return "{$this->systolic}/{$this->diastolic} mmHg";
        }

        return null;
    }

    public function dietCompliantLabel(): ?string
    {
        if ($this->diet_compliant === null) {
            return null;
        }

        return $this->diet_compliant ? 'Ya' : 'Tidak';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
