<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreeningSession extends Model
{
    protected $fillable = [
        'user_id',
        'answers',
        'summary',
        'risk_level',
        'is_emergency',
    ];

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
}
