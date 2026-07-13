<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMedication extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'dose',
        'schedule',
        'prescription_days',
        'purpose',
        'doctor_name',
        'notes',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function summaryLine(): string
    {
        $parts = array_filter([
            $this->dose,
            $this->schedule,
        ]);

        return $parts === [] ? $this->name : implode(' · ', $parts);
    }
}
