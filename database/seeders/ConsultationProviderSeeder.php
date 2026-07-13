<?php

namespace Database\Seeders;

use App\Models\ConsultationProvider;
use Illuminate\Database\Seeder;

class ConsultationProviderSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('consultation.providers', []) as $key => $item) {
            if (! is_array($item)) {
                continue;
            }

            ConsultationProvider::query()->updateOrCreate(
                ['key' => $key],
                [
                    'category_key' => (string) ($item['category'] ?? $key),
                    'active' => (bool) ($item['active'] ?? true),
                    'name' => (string) ($item['name'] ?? $key),
                    'short_name' => (string) ($item['short_name'] ?? $item['name'] ?? $key),
                    'title' => $item['title'] ?? null,
                    'specialty' => $item['specialty'] ?? null,
                    'credential' => $item['credential'] ?? null,
                    'experience_years' => isset($item['experience_years']) ? (int) $item['experience_years'] : null,
                    'rating_percent' => isset($item['rating_percent']) ? (int) $item['rating_percent'] : null,
                    'price' => null,
                    'photo' => $item['photo'] ?? null,
                    'icon' => $item['icon'] ?? null,
                    'whatsapp' => (string) ($item['whatsapp'] ?? ''),
                    'whatsapp_intl' => $item['whatsapp_intl'] ?? null,
                    'greeting' => $item['greeting'] ?? null,
                    'sort_order' => 0,
                ]
            );
        }
    }
}
