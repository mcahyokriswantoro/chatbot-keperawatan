<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AyosehatArticle extends Model
{
    protected $fillable = [
        'external_slug',
        'category_id',
        'category_name',
        'title',
        'excerpt',
        'url',
        'image_url',
        'tag',
        'read_min',
        'published_at',
        'is_active',
        'synced_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'date',
            'is_active' => 'boolean',
            'synced_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
