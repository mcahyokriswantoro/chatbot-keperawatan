<?php

return [
    'emergency' => [
        'title' => 'Segera ke rumah sakit (waktu emas < 4,5 jam) jika Anda mengalami:',
        'items' => [
            'Wajah tiba-tiba mencong',
            'Salah satu lengan lemah atau tidak bisa diangkat',
            'Bicara pelo atau tidak jelas',
            'Gejala muncul secara mendadak',
        ],
    ],
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'intro' => 'Risiko stroke Anda relatif rendah. Pertahankan gaya hidup sehat untuk mencegah masalah di kemudian hari.',
        'sections' => [
            ['title' => 'Makan Sehat', 'items' => [
                'Kurangi garam dan lemak jenuh',
                'Perbanyak buah dan sayur',
                'Batasi gula dan makanan olahan',
            ]],
            ['title' => 'Aktif Bergerak', 'items' => [
                'Olahraga minimal 150 menit per minggu, seperti jalan cepat atau bersepeda',
            ]],
            ['title' => 'Hindari Kebiasaan Berisiko', 'items' => [
                'Berhenti merokok',
                'Batasi alkohol',
                'Kelola stres dengan baik',
            ]],
            ['title' => 'Pantau Kesehatan', 'items' => [
                'Cek tekanan darah secara berkala',
                'Lakukan skrining rutin',
            ]],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'intro' => 'Beberapa faktor risiko perlu dikendalikan agar risiko stroke tidak meningkat.',
        'sections' => [
            ['title' => 'Kendalikan Faktor Risiko Utama', 'items' => [
                'Jaga tekanan darah tetap normal',
                'Kendalikan gula darah jika punya diabetes',
                'Turunkan kolesterol jika tinggi',
            ]],
            ['title' => 'Diet Sehat untuk Jantung dan Otak', 'items' => [
                'Ikuti pola makan DASH atau Mediterania',
                'Batasi garam kurang dari 5 gram per hari',
                'Kurangi makanan gorengan dan lemak jenuh',
            ]],
            ['title' => 'Olahraga Teratur', 'items' => [
                'Olahraga ringan sampai sedang 30 menit setiap hari',
            ]],
            ['title' => 'Minum Obat Sesuai Resep', 'items' => [
                'Jika sudah ada obat dari dokter, minum secara teratur',
            ]],
            ['title' => 'Pantau Secara Berkala', 'items' => [
                'Tekanan darah',
                'Gula darah',
                'Kolesterol',
            ]],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi',
        'intro' => 'Risiko stroke tinggi. Kontrol rutin ke dokter dan patuhi terapi sangat penting.',
        'sections' => [
            ['title' => 'Kontrol Medis Rutin', 'items' => [
                'Jangan lewatkan jadwal kontrol ke dokter',
                'Lakukan evaluasi jantung, pembuluh darah, dan otak sesuai anjuran',
            ]],
            ['title' => 'Patuhi Pengobatan', 'items' => [
                'Minum obat antihipertensi jika diresepkan',
                'Minum obat pengencer darah/antiplatelet sesuai resep',
                'Minum obat diabetes atau kolesterol jika diperlukan',
                'Jangan berhenti obat tanpa berkonsultasi dengan dokter',
            ]],
            ['title' => 'Rehabilitasi dan Latihan', 'items' => [
                'Lakukan latihan fisik sesuai kemampuan',
                'Latihan bicara jika ada gangguan bicara',
                'Latihan keseimbangan untuk mencegah jatuh',
            ]],
            ['title' => 'Atur Aktivitas', 'items' => [
                'Istirahat cukup',
                'Hindari kelelahan berlebihan',
            ]],
            ['title' => 'Nutrisi yang Mendukung', 'items' => [
                'Diet rendah garam',
                'Tinggi serat',
                'Kurangi lemak jenuh',
            ]],
            ['title' => 'Dukungan Keluarga', 'items' => [
                'Minta pendamping saat beraktivitas',
                'Ajak keluarga mengingatkan minum obat',
                'Pelajari bersama tanda bahaya stroke',
            ]],
        ],
    ],
];
