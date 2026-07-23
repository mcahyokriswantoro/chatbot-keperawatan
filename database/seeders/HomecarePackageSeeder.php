<?php

namespace Database\Seeders;

use App\Models\HomecarePackage;
use Illuminate\Database\Seeder;

class HomecarePackageSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Perawatan Luka Ringan',
                'description' => 'Perawatan luka lecet, luka jatuh, atau luka pasca operasi kecil. Termasuk bahan medis habis pakai (kassa steril, antiseptik, perban).',
                'price' => 150000,
                'icon' => '🩹',
                'active' => true,
            ],
            [
                'name' => 'Perawatan Luka Diabetes',
                'description' => 'Perawatan luka kronis/diabetes oleh perawat spesialis luka. Termasuk debridement ringan, pencucian luka, dan pembalut luka khusus.',
                'price' => 250000,
                'icon' => '🩺',
                'active' => true,
            ],
            [
                'name' => 'Pemasangan / Penggantian Infus',
                'description' => 'Pemasangan jalur infus baru atau penggantian cairan infus di rumah. Tidak termasuk obat injeksi (harus ada resep dokter).',
                'price' => 150000,
                'icon' => '💧',
                'active' => true,
            ],
            [
                'name' => 'Pemasangan Kateter Urin',
                'description' => 'Pemasangan kateter urin baru atau penggantian kateter urin berkala secara steril.',
                'price' => 175000,
                'icon' => '💉',
                'active' => true,
            ],
            [
                'name' => 'Fisioterapi Pasca Stroke',
                'description' => 'Latihan fisik pasca stroke atau latihan gerak fungsional untuk mengembalikan mobilitas oleh terapis terampil.',
                'price' => 200000,
                'icon' => '🏃',
                'active' => true,
            ],
            [
                'name' => 'Pendampingan Lansia Harian (8 Jam)',
                'description' => 'Kunjungan perawat/caregiver selama 8 jam untuk memantau tanda vital, membantu aktivitas makan, mandi, minum obat, dan mobilitas lansia.',
                'price' => 300000,
                'icon' => '👴',
                'active' => true,
            ],
        ];

        foreach ($packages as $pkg) {
            HomecarePackage::updateOrCreate(
                ['name' => $pkg['name']],
                $pkg
            );
        }

        \App\Models\Setting::updateOrCreate(
            ['key' => 'homecare_transport_fee_per_km'],
            ['value' => '5000']
        );

        \App\Models\Setting::updateOrCreate(
            ['key' => 'medicine_shipping_fee_per_km'],
            ['value' => '3000']
        );
    }
}
