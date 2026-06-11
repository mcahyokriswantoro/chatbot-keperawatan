<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HealthArticle extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'cover_image',
        'excerpt',
        'content',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function coverImageUrl(): ?string
    {
        if ($this->cover_image && Storage::disk('public')->exists($this->cover_image)) {
            return Storage::disk('public')->url($this->cover_image);
        }

        return null;
    }
}
