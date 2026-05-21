<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScreeningSession extends Model
{
    protected $fillable = [
        'user_id',
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
