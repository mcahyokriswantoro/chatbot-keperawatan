<?php

namespace App\Services;

use App\Models\ConsultationProvider;
use App\Models\User;

class ConsultationWhatsAppService
{
    public function provider(string $key): ?array
    {
        return ConsultationProvider::profileForKey($key);
    }

    public function categoryMeta(string $categoryKey): ?array
    {
        $category = collect(config('consultation.categories', []))
            ->firstWhere('key', $categoryKey);

        if (! is_array($category)) {
            return null;
        }

        if ($category['active'] ?? false) {
            return $category;
        }

        if (ConsultationProvider::tableReady()) {
            $hasProviders = ConsultationProvider::query()
                ->where('category_key', $categoryKey)
                ->where('active', true)
                ->exists();

            if ($hasProviders) {
                return $category;
            }
        }

        if ($this->hasSubcategories($categoryKey)) {
            return $category;
        }

        return null;
    }

    public function hasSubcategories(string $categoryKey): bool
    {
        return collect(config('consultation.categories', []))
            ->contains(fn (array $cat) => ($cat['parent_key'] ?? null) === $categoryKey);
    }

    /**
     * @return array<int, array{key: string, name: string, short_name: string, specialty: string, experience_years: int|null, rating_percent: int|null, photo: string, price: int, price_label: string}>
     */
    public function providersForCategory(string $categoryKey, ConsultationAccessService $access): array
    {
        if ($this->categoryMeta($categoryKey) === null) {
            return [];
        }

        $result = [];

        if (ConsultationProvider::tableReady()) {
            $models = ConsultationProvider::query()
                ->where('category_key', $categoryKey)
                ->where('active', true)
                ->orderBy('sort_order')
                ->orderBy('short_name')
                ->get();

            foreach ($models as $model) {
                $price = $access->priceFor($model->key);

                $result[] = [
                    'key' => $model->key,
                    'name' => $model->name,
                    'short_name' => $model->short_name,
                    'specialty' => $model->specialty ?? $model->title ?? '',
                    'experience_years' => $model->experience_years,
                    'rating_percent' => $model->rating_percent,
                    'photo' => $model->photoUrl(),
                    'price' => $price,
                    'price_label' => $access->formatRupiah($price),
                ];
            }
        }

        if ($result !== []) {
            return $result;
        }

        $providers = config('consultation.providers', []);

        foreach ($providers as $key => $provider) {
            if (! is_array($provider) || ! ($provider['active'] ?? false)) {
                continue;
            }

            $providerCategory = (string) ($provider['category'] ?? $key);

            if ($providerCategory !== $categoryKey) {
                continue;
            }

            $price = $access->priceFor($key);
            $photo = (string) ($provider['photo'] ?? 'images/avatars/male.svg');

            $result[] = [
                'key' => $key,
                'name' => (string) ($provider['name'] ?? ''),
                'short_name' => (string) ($provider['short_name'] ?? $provider['name'] ?? ''),
                'specialty' => (string) ($provider['specialty'] ?? $provider['title'] ?? ''),
                'experience_years' => isset($provider['experience_years']) ? (int) $provider['experience_years'] : null,
                'rating_percent' => isset($provider['rating_percent']) ? (int) $provider['rating_percent'] : null,
                'photo' => str_starts_with($photo, 'http') ? $photo : '/'.ltrim($photo, '/'),
                'price' => $price,
                'price_label' => $access->formatRupiah($price),
            ];
        }

        return $result;
    }

    public function internationalNumber(string $key): string
    {
        $provider = $this->provider($key);

        if (! $provider) {
            return '';
        }

        if (! empty($provider['whatsapp_intl'])) {
            return preg_replace('/\D+/', '', (string) $provider['whatsapp_intl']);
        }

        return $this->normalizeIndonesianNumber((string) ($provider['whatsapp'] ?? ''));
    }

    public function buildMessageUrl(string $providerKey, string $message, ?User $user = null): string
    {
        $number = $this->internationalNumber($providerKey);
        $provider = $this->provider($providerKey);

        if ($number === '' || ! $provider) {
            return '';
        }

        $lines = [
            'Halo '.$provider['name'].',',
            '',
            'Saya ingin konsultasi via Chatbot Keperawatan:',
            '',
            trim($message),
        ];

        if ($user?->name) {
            $lines[] = '';
            $lines[] = '— '.$user->name;
        }

        return $this->buildRawUrl($providerKey, implode("\n", $lines));
    }

    public function buildDirectUrl(string $providerKey, ?User $user = null): string
    {
        return $this->buildLiveStartUrl($providerKey, $user, 'konsultasi');
    }

    public function buildLiveStartUrl(string $providerKey, ?User $user = null, string $via = 'pembayaran'): string
    {
        return $this->buildRawUrl(
            $providerKey,
            $this->buildLiveStartMessage($providerKey, $user, $via),
        );
    }

    public function buildLiveStartMessage(string $providerKey, ?User $user = null, string $via = 'pembayaran'): string
    {
        $provider = $this->provider($providerKey);
        if (! $provider) {
            return '';
        }

        $viaLabel = match ($via) {
            'voucher' => 'voucher 100%',
            'dana' => 'pembayaran DANA',
            default => 'pembayaran konsultasi',
        };

        $role = str_contains(strtolower((string) ($provider['specialty'] ?? '')), 'dokter') ? 'dokter' : 'perawat';

        $lines = [
            'Halo '.$provider['name'].',',
            '',
            'Saya ingin konsultasi LIVE dengan '.$role.'.',
            'Saya sudah menyelesaikan '.$viaLabel.' di Chatbot Keperawatan.',
            'Mohon bantuannya.',
        ];

        if ($user?->name) {
            $lines[] = '';
            $lines[] = '— '.$user->name;
            if ($user->phone) {
                $lines[] = $user->phone;
            }
        }

        return implode("\n", $lines);
    }

    public function displayNumber(string $providerKey): string
    {
        $intl = $this->internationalNumber($providerKey);

        if ($intl === '') {
            return '';
        }

        if (str_starts_with($intl, '62')) {
            return '0'.substr($intl, 2);
        }

        return $intl;
    }

    private function buildRawUrl(string $providerKey, string $text): string
    {
        $number = $this->internationalNumber($providerKey);

        if ($number === '') {
            return '';
        }

        $base = rtrim((string) config('consultation.whatsapp.api_base', 'https://api.whatsapp.com/send'), '/');

        if (str_contains($base, 'wa.me')) {
            return "{$base}/{$number}?text=".rawurlencode($text);
        }

        return "{$base}?phone={$number}&text=".rawurlencode($text);
    }

    private function normalizeIndonesianNumber(string $number): string
    {
        $digits = preg_replace('/\D+/', '', $number) ?? '';

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
}
