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
        'content_type',
        'cover_image',
        'video_url',
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

    public function isVideo(): bool
    {
        return ($this->content_type ?? 'article') === 'video';
    }

    public function isArticle(): bool
    {
        return ! $this->isVideo();
    }

    public function contentTypeLabel(): string
    {
        return $this->isVideo() ? 'Video' : 'Artikel';
    }

    public function coverImageUrl(): ?string
    {
        if ($this->cover_image && Storage::disk('public')->exists($this->cover_image)) {
            return $this->publicStorageUrl($this->cover_image);
        }

        return null;
    }

    public function hasStoredVideo(): bool
    {
        return (bool) $this->video_url && ! str_starts_with($this->video_url, 'http');
    }

    public function videoPlaybackUrl(): ?string
    {
        if ($this->hasStoredVideo()) {
            return Storage::disk('public')->exists($this->video_url)
                ? $this->publicStorageUrl($this->video_url)
                : null;
        }

        return $this->videoEmbedUrl();
    }

    public function videoMimeType(): ?string
    {
        if (! $this->hasStoredVideo()) {
            return null;
        }

        return match (strtolower(pathinfo($this->video_url, PATHINFO_EXTENSION))) {
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'mov' => 'video/quicktime',
            default => 'video/mp4',
        };
    }

    protected function publicStorageUrl(string $path): string
    {
        return '/storage/'.ltrim($path, '/');
    }

    public function videoEmbedUrl(): ?string
    {
        if (! $this->video_url || $this->hasStoredVideo()) {
            return null;
        }

        $url = trim($this->video_url);

        if (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([a-zA-Z0-9_-]{11})~', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $url, $matches)) {
            return 'https://player.vimeo.com/video/'.$matches[1];
        }

        if (preg_match('~\.(mp4|webm|ogg)(\?|$)~i', $url)) {
            return $url;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    public function isDirectVideoFile(): bool
    {
        if ($this->hasStoredVideo()) {
            return true;
        }

        $embed = $this->videoEmbedUrl();

        return $embed && preg_match('~\.(mp4|webm|ogg)(\?|$)~i', $embed);
    }
}
