<?php

namespace Database\Seeders;

use App\Models\HealthArticle;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HealthFeatureSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@chatsimpel.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'apotek@chatsimpel.test'],
            [
                'name' => 'Apoteker Dummy',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'provider_key' => 'apotek',
                'email_verified_at' => now(),
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'homecare@chatsimpel.test'],
            [
                'name' => 'Perawat Homecare Dummy',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'provider_key' => 'homecare',
                'email_verified_at' => now(),
            ]
        );

        $articles = [
            [
                'title' => 'Pentingnya Cek Tekanan Darah Rutin',
                'slug' => 'cek-tekanan-darah-rutin',
                'category' => 'Pencegahan',
                'excerpt' => 'Tekanan darah tinggi sering tanpa gejala. Pantau secara berkala untuk mencegah komplikasi.',
                'content' => "Hipertensi adalah faktor risiko utama stroke dan penyakit jantung.\n\nDisarankan memeriksa tekanan darah minimal sekali sebulan, atau lebih sering jika memiliki riwayat keluarga hipertensi.\n\nCatat hasil pengukuran di fitur Monitoring Kesehatan aplikasi ini.",
            ],
            [
                'title' => 'Mengenali Gejala Darurat Jantung',
                'slug' => 'gejala-darurat-jantung',
                'category' => 'Darurat',
                'excerpt' => 'Nyeri dada dan sesak napas memerlukan penanganan segera.',
                'content' => "Gejala yang perlu waspada:\n- Nyeri dada yang menjalar ke lengan atau rahang\n- Sesak napas mendadak\n- Keringat dingin dan mual hebat\n\nSegera hubungi 119 atau kunjungi IGD terdekat.",
            ],
            [
                'title' => 'Tips Manajemen Diabetes di Rumah',
                'slug' => 'manajemen-diabetes-rumah',
                'category' => 'Self Management',
                'excerpt' => 'Kontrol gula darah, diet, dan aktivitas fisik adalah kunci.',
                'content' => "Pasien diabetes disarankan:\n1. Minum obat sesuai jadwal\n2. Batasi gula dan karbohidrat sederhana\n3. Olahraga ringan 30 menit per hari\n4. Catat gula darah secara rutin\n\nGunakan fitur Self Management untuk mengingatkan aktivitas harian.",
            ],
        ];

        foreach ($articles as $article) {
            HealthArticle::query()->updateOrCreate(
                ['slug' => $article['slug']],
                $article + ['is_published' => true]
            );
        }
    }
}
