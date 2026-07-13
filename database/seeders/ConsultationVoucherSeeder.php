<?php

namespace Database\Seeders;

use App\Models\ConsultationVoucher;
use Illuminate\Database\Seeder;

class ConsultationVoucherSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('consultation.demo_vouchers', []) as $item) {
            ConsultationVoucher::query()->updateOrCreate(
                ['code' => strtoupper($item['code'])],
                [
                    'discount_percent' => $item['discount_percent'],
                    'provider_key' => $item['provider_key'] ?? null,
                    'max_uses' => $item['max_uses'] ?? 100,
                    'uses_count' => 0,
                    'is_active' => true,
                    'expires_at' => null,
                ]
            );
        }
    }
}
