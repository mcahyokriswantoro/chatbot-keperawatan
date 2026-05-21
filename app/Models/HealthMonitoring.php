<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HealthMonitoring extends Model
{
    protected $fillable = [
        'user_id',
        'systolic',
        'diastolic',
        'heart_rate',
        'blood_sugar',
        'weight',
        'notes',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at' => 'date',
            'blood_sugar' => 'decimal:1',
            'weight' => 'decimal:1',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
