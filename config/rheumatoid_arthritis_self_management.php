<?php

return [
    'emergency' => [
        'title' => 'Segera ke rumah sakit jika Anda mengalami:',
        'items' => [
            'Nyeri sendi sangat berat dan mendadak memburuk',
            'Pembengkakan sendi yang cepat dan luas',
            'Demam tinggi disertai nyeri sendi berat',
            'Kesulitan berjalan atau menggunakan tangan secara tiba-tiba',
            'Sesak napas atau nyeri dada',
            'Efek samping obat seperti ruam berat, perdarahan, atau sesak napas',
            'Penurunan kemampuan aktivitas sehari-hari secara signifikan',
        ],
    ],

    'Rendah' => [
        'label' => 'Risiko Rendah',
        'intro' => 'Gejala sendi relatif ringan. Pertahankan gaya hidup sehat dan pantau perubahan keluhan.',
        'sections' => [
            ['title' => 'Lakukan Aktivitas Fisik Ringan dan Teratur', 'items' => [
                'Lakukan peregangan sendi setiap hari',
                'Jalan kaki, bersepeda santai, atau senam ringan',
                'Hindari duduk atau berbaring terlalu lama',
            ]],
            ['title' => 'Jaga Berat Badan Ideal', 'items' => [
                'Pertahankan berat badan normal',
                'Hindari kelebihan berat badan yang dapat membebani sendi',
            ]],
            ['title' => 'Terapkan Pola Makan Sehat', 'items' => [
                'Perbanyak buah dan sayuran',
                'Konsumsi ikan yang mengandung omega-3',
                'Kurangi makanan tinggi gula dan lemak jenuh',
            ]],
            ['title' => 'Hindari Faktor Risiko', 'items' => [
                'Tidak merokok',
                'Hindari paparan asap rokok',
                'Kelola stres dengan baik',
            ]],
            ['title' => 'Lakukan Monitoring Mandiri', 'items' => [
                'Perhatikan munculnya nyeri atau pembengkakan sendi',
                'Catat bila kekakuan sendi terjadi berulang',
            ]],
        ],
    ],

    'Sedang' => [
        'label' => 'Risiko Sedang',
        'intro' => 'Ada tanda gejala sendi yang perlu diwaspadai. Segera konsultasikan ke tenaga kesehatan untuk pemeriksaan lebih lanjut.',
        'sections' => [
            ['title' => 'Lakukan Latihan Rentang Gerak Sendi (Range of Motion Exercise)', 'items' => [
                'Latihan sendi minimal 15–30 menit/hari',
                'Gerakkan sendi secara perlahan dan teratur',
            ]],
            ['title' => 'Lakukan Manajemen Nyeri', 'items' => [
                'Kompres hangat pada sendi yang kaku',
                'Istirahatkan sendi saat nyeri meningkat',
                'Hindari aktivitas yang terlalu berat',
            ]],
            ['title' => 'Terapkan Diet Antiinflamasi', 'items' => [
                'Konsumsi ikan laut (salmon, tuna, sarden)',
                'Perbanyak sayuran hijau dan buah-buahan',
                'Kurangi makanan cepat saji dan makanan tinggi gula',
            ]],
            ['title' => 'Lakukan Manajemen Stres', 'items' => [
                'Relaksasi, meditasi, dzikir, atau aktivitas spiritual',
                'Tidur cukup 7–8 jam setiap malam',
            ]],
            ['title' => 'Lakukan Monitoring Rutin', 'items' => [
                'Catat tingkat nyeri sendi setiap hari (skala 0–10)',
                'Catat lama kekakuan pagi hari',
                'Pantau pembengkakan atau kemerahan pada sendi',
            ]],
            ['title' => 'Konsultasi ke Fasilitas Kesehatan', 'items' => [
                'Bila keluhan menetap lebih dari 6 minggu',
                'Bila aktivitas sehari-hari mulai terganggu',
            ]],
        ],
    ],

    'Tinggi' => [
        'label' => 'Risiko Tinggi',
        'intro' => 'Skor skrining menunjukkan risiko tinggi rheumatoid arthritis. Segera konsultasikan ke dokter spesialis untuk pemeriksaan dan penanganan.',
        'sections' => [
            ['title' => 'Lakukan Tindakan Utama', 'items' => [
                'Kontrol rutin ke dokter atau spesialis reumatologi',
                'Evaluasi perkembangan penyakit secara berkala',
            ]],
            ['title' => 'Lakukan Kepatuhan Terapi', 'items' => [
                'Minum obat sesuai resep dokter',
                'Tidak menghentikan obat tanpa konsultasi',
                'Patuhi jadwal kontrol yang telah ditentukan',
            ]],
            ['title' => 'Lakukan Perlindungan Sendi (Joint Protection)', 'items' => [
                'Hindari mengangkat beban berat',
                'Gunakan alat bantu bila diperlukan',
                'Atur aktivitas dan waktu istirahat secara seimbang',
            ]],
            ['title' => 'Lakukan Aktivitas Fisik yang Aman', 'items' => [
                'Latihan rentang gerak sendi setiap hari',
                'Latihan penguatan otot sesuai anjuran tenaga kesehatan',
                'Hindari olahraga dengan benturan tinggi saat sendi meradang',
            ]],
            ['title' => 'Terapkan Diet Antiinflamasi', 'items' => [
                'Tingkatkan konsumsi omega-3',
                'Perbanyak sayuran, buah, dan serat',
                'Batasi makanan tinggi gula, garam, dan lemak jenuh',
            ]],
            ['title' => 'Lakukan Monitoring Mandiri', 'items' => [
                'Catat jumlah sendi yang nyeri atau bengkak',
                'Catat tingkat nyeri harian (0–10)',
                'Catat lama kekakuan pagi hari',
                'Catat kemampuan melakukan aktivitas sehari-hari',
            ]],
            ['title' => 'Lakukan Dukungan Keluarga', 'items' => [
                'Membantu mengingatkan minum obat',
                'Membantu aktivitas saat nyeri berat',
                'Memberikan dukungan emosional dan motivasi',
            ]],
        ],
    ],
];
