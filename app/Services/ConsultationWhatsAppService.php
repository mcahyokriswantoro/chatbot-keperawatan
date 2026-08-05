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

    public static function normalizeCategoryKey(string $key): string
    {
        $normalized = str_replace('-', '_', strtolower(trim($key)));

        return match ($normalized) {
            'perawat', 'perawat_ners', 'ners' => 'perawat',
            'dokter', 'dokter_umum', 'umum' => 'dokter_umum',
            'penyakit_dalam', 'dokter_spesialis', 'spesialis' => 'penyakit_dalam',
            default => $normalized,
        };
    }

    public static function categoryAliases(string $key): array
    {
        $normalized = self::normalizeCategoryKey($key);

        return match ($normalized) {
            'perawat' => ['perawat', 'perawat-ners', 'perawat_ners', 'ners'],
            'dokter_umum' => ['dokter', 'dokter_umum', 'dokter-umum', 'umum'],
            'penyakit_dalam' => ['penyakit_dalam', 'penyakit-dalam', 'dokter_spesialis', 'dokter-spesialis', 'spesialis'],
            default => [$normalized, str_replace('_', '-', $normalized), str_replace('-', '_', $normalized)],
        };
    }

    public function categoryMeta(string $categoryKey): ?array
    {
        $canonicalKey = self::normalizeCategoryKey($categoryKey);
        $aliases = self::categoryAliases($categoryKey);

        $category = collect(config('consultation.categories', []))
            ->first(function (array $cat) use ($aliases) {
                $catKey = (string) ($cat['key'] ?? '');

                return in_array(self::normalizeCategoryKey($catKey), $aliases, true);
            });

        if (! is_array($category)) {
            if ($canonicalKey === 'dokter_umum') {
                return [
                    'key' => 'dokter_umum',
                    'label' => 'Dokter Umum',
                    'icon' => '👨‍⚕️',
                    'description' => 'Konsultasi keluhan umum, interpretasi hasil skrining, dan rujukan lanjut.',
                    'active' => true,
                    'primary' => true,
                ];
            }
            if ($canonicalKey === 'penyakit_dalam') {
                return [
                    'key' => 'penyakit_dalam',
                    'label' => 'Dokter Spesialis Penyakit Dalam',
                    'icon' => '🫀',
                    'description' => 'Konsultasi gangguan metabolik, diabetes, hipertensi, dan lainnya.',
                    'active' => true,
                    'primary' => true,
                ];
            }

            return null;
        }

        $category['active'] = true;

        return $category;
    }

    public function hasSubcategories(string $categoryKey): bool
    {
        $aliases = self::categoryAliases($categoryKey);

        return collect(config('consultation.categories', []))
            ->contains(fn (array $cat) => in_array(self::normalizeCategoryKey((string) ($cat['parent_key'] ?? '')), $aliases, true));
    }

    /**
     * @return array<int, array{key: string, name: string, short_name: string, specialty: string, experience_years: int|null, rating_percent: int|null, photo: string, price: int, price_label: string}>
     */
    public function providersForCategory(string $categoryKey, ConsultationAccessService $access): array
    {
        if ($this->categoryMeta($categoryKey) === null) {
            return [];
        }

        $aliases = self::categoryAliases($categoryKey);
        $canonicalKey = self::normalizeCategoryKey($categoryKey);

        // Auto-sync approved Nakes users from users table
        $nakesUsers = User::query()
            ->whereIn('provider_key', ['dokter', 'perawat'])
            ->where('is_approved', true)
            ->get();

        foreach ($nakesUsers as $nakes) {
            ConsultationProvider::syncFromUser($nakes);
        }

        $result = [];

        if (ConsultationProvider::tableReady()) {
            $models = ConsultationProvider::query()
                ->where('active', true)
                ->where(function ($query) use ($aliases, $canonicalKey) {
                    $query->whereIn('category_key', $aliases);

                    if ($canonicalKey === 'perawat') {
                        $query->orWhere('category_key', 'perawat')
                            ->orWhere('specialty', 'like', '%perawat%')
                            ->orWhere('specialty', 'like', '%ners%');
                    } elseif ($canonicalKey === 'dokter_umum') {
                        $query->orWhere('category_key', 'dokter_umum')
                            ->orWhere('category_key', 'dokter');
                    } elseif ($canonicalKey === 'penyakit_dalam') {
                        $query->orWhere('category_key', 'penyakit_dalam')
                            ->orWhere('specialty', 'like', '%spesialis%')
                            ->orWhere('specialty', 'like', '%Sp.%');
                    }
                })
                ->orderBy('sort_order')
                ->orderBy('short_name')
                ->get();

            foreach ($models as $model) {
                $isPerawat = \Illuminate\Support\Str::contains(strtolower($model->specialty ?? ''), ['perawat', 'ners']) || $model->category_key === 'perawat';
                $isSp = \Illuminate\Support\Str::contains(strtolower($model->name), ['sp.', 'spesialis']) || \Illuminate\Support\Str::contains(strtolower($model->specialty ?? ''), ['sp.', 'spesialis']);

                if ($canonicalKey === 'perawat' && ! $isPerawat) {
                    continue;
                }

                if ($canonicalKey === 'dokter_umum' && ($isPerawat || $isSp)) {
                    continue;
                }

                if ($canonicalKey === 'penyakit_dalam' && ! $isSp && $model->category_key !== 'penyakit_dalam') {
                    continue;
                }

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
