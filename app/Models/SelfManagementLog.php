<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfManagementLog extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'title',
        'notes',
        'completed',
        'scheduled_for',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'scheduled_for' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
