<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthTip extends Model
{
    protected $fillable = [
        'content',
        'source_slug',
        'source_url',
        'week_start',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
        ];
    }
}
