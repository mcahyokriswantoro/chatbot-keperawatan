<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ConsultationProvider extends Model
{
    protected $fillable = [
        'key',
        'category_key',
        'active',
        'name',
        'short_name',
        'title',
        'specialty',
        'credential',
        'experience_years',
        'rating_percent',
        'price',
        'photo',
        'icon',
        'whatsapp',
        'whatsapp_intl',
        'greeting',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'experience_years' => 'integer',
            'rating_percent' => 'integer',
            'price' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'key';
    }

    public function photoUrl(): string
    {
        if (! $this->photo) {
            return '/images/avatars/male.svg';
        }

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            return $this->photo;
        }

        if (str_starts_with($this->photo, 'images/')) {
            return '/'.$this->photo.'?v='.($this->updated_at?->timestamp ?? '1');
        }

        return '/storage/'.ltrim($this->photo, '/').'?v='.($this->updated_at?->timestamp ?? '1');
    }

    public function categoryLabel(): string
    {
        $category = collect(config('consultation.categories', []))
            ->firstWhere('key', $this->category_key);

        return is_array($category) ? (string) ($category['label'] ?? $this->category_key) : $this->category_key;
    }

    /**
     * @return array<string, mixed>
     */
    public function toProfileArray(): array
    {
        return [
            'key' => $this->key,
            'active' => $this->active,
            'category' => $this->category_key,
            'name' => $this->name,
            'short_name' => $this->short_name,
            'title' => $this->title ?? '',
            'specialty' => $this->specialty ?? $this->title ?? '',
            'credential' => $this->credential ?? '',
            'experience_years' => $this->experience_years,
            'rating_percent' => $this->rating_percent,
            'photo' => $this->photoUrl(),
            'icon' => $this->icon ?? '👩‍⚕️',
            'whatsapp' => $this->whatsapp,
            'whatsapp_intl' => $this->whatsapp_intl ?? '',
            'greeting' => $this->greeting ?? '',
            'price' => $this->price,
        ];
    }

    public static function tableReady(): bool
    {
        return Schema::hasTable('consultation_providers');
    }

    public static function findActiveByKey(string $key): ?self
    {
        if (! self::tableReady()) {
            return null;
        }

        return self::query()
            ->where('key', $key)
            ->where('active', true)
            ->first();
    }

    public static function profileForKey(string $key): ?array
    {
        $model = self::findActiveByKey($key);

        if ($model) {
            return $model->toProfileArray();
        }

        $config = config("consultation.providers.{$key}");

        if (! is_array($config) || ! ($config['active'] ?? false)) {
            return null;
        }

        $photo = (string) ($config['photo'] ?? 'images/avatars/male.svg');

        return array_merge($config, [
            'key' => $key,
            'photo' => self::publicAssetUrl($photo),
        ]);
    }

    private static function publicAssetUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return '/'.ltrim($path, '/');
    }

    public static function generateKey(string $shortName, ?string $categoryKey = null): string
    {
        $base = Str::slug($shortName);

        if ($base === '') {
            $base = 'tenaga-kesehatan';
        }

        $key = $categoryKey ? "{$categoryKey}-{$base}" : $base;
        $original = $key;
        $i = 2;

        while (self::query()->where('key', $key)->exists()) {
            $key = "{$original}-{$i}";
            $i++;
        }

        return $key;
    }

    public static function normalizeWhatsappIntl(string $whatsapp, ?string $intl = null): string
    {
        if ($intl) {
            return preg_replace('/\D+/', '', $intl) ?? '';
        }

        $digits = preg_replace('/\D+/', '', $whatsapp) ?? '';

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        return '62'.$digits;
    }

    public static function syncFromUser(User $user): ?self
    {
        if (! self::tableReady() || ! $user->provider_key || ! in_array($user->provider_key, ['dokter', 'perawat'], true)) {
            return null;
        }

        $slugKey = Str::slug($user->provider_key.'-'.$user->name);
        $provider = self::query()
            ->where('key', $user->provider_key)
            ->orWhere('key', $slugKey)
            ->orWhere('name', $user->name)
            ->orWhere('whatsapp', $user->phone)
            ->first();

        $isSpesialis = $user->provider_key === 'dokter' && (
            Str::contains(strtolower($user->name), ['sp.', 'spesialis']) ||
            Str::contains(strtolower($user->occupation ?? ''), ['sp.', 'spesialis'])
        );

        $categoryKey = $user->provider_key === 'dokter'
            ? ($isSpesialis ? 'penyakit_dalam' : 'dokter_umum')
            : 'perawat';

        $price = $user->provider_key === 'dokter'
            ? ($isSpesialis ? 150000 : 100000)
            : 100000;

        if ($provider) {
            $provider->update([
                'category_key' => $provider->category_key ?: $categoryKey,
                'name' => $user->name,
                'short_name' => $provider->short_name ?: $user->name,
                'whatsapp' => $user->phone ?? $provider->whatsapp,
                'whatsapp_intl' => self::normalizeWhatsappIntl($user->phone ?? $provider->whatsapp),
                'specialty' => $user->occupation ?? $provider->specialty,
                'photo' => $user->profile_photo ?? $provider->photo,
                'price' => $provider->price ?? $price,
            ]);

            return $provider;
        }

        $key = self::generateKey($user->name, $user->provider_key);

        return self::create([
            'key' => $key,
            'category_key' => $categoryKey,
            'active' => true,
            'name' => $user->name,
            'short_name' => $user->name,
            'title' => $user->provider_key === 'dokter' ? ($isSpesialis ? 'Dokter Spesialis' : 'Dokter Umum') : 'Perawat (Ners)',
            'specialty' => $user->occupation ?? ($user->provider_key === 'dokter' ? 'Dokter' : 'Perawat'),
            'experience_years' => 5,
            'rating_percent' => 100,
            'price' => $price,
            'icon' => $user->provider_key === 'dokter' ? ($isSpesialis ? '🫀' : '👨‍⚕️') : '👩‍⚕️',
            'whatsapp' => $user->phone ?? '081234567890',
            'whatsapp_intl' => self::normalizeWhatsappIntl($user->phone ?? '081234567890'),
            'greeting' => "Halo! Saya {$user->name}. Ada yang bisa saya bantu terkait keluhan kesehatan Anda?",
            'sort_order' => 1,
            'photo' => $user->profile_photo,
        ]);
    }
}
