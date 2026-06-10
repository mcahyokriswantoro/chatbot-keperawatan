<?php

return [
    'emergency' => [
        'title' => 'Segera ke rumah sakit jika Anda mengalami:',
        'items' => [
            'Tekanan darah 180/120 mmHg atau lebih',
            'Nyeri dada',
            'Sesak napas',
            'Gangguan penglihatan atau bicara',
        ],
    ],
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'intro' => 'Tekanan darah Anda relatif baik. Pertahankan gaya hidup sehat agar tetap terkendali.',
        'sections' => [
            ['title' => 'Makan Sehat', 'items' => [
                'Batasi garam kurang dari 5 gram per hari',
                'Perbanyak buah dan sayur',
                'Kurangi makanan instan dan olahan',
            ]],
            ['title' => 'Aktif Bergerak', 'items' => [
                'Olahraga minimal 150 menit per minggu',
                'Jalan cepat, bersepeda, atau senam',
            ]],
            ['title' => 'Jaga Berat Badan', 'items' => [
                'Usahakan berat badan tetap ideal',
            ]],
            ['title' => 'Hindari Kebiasaan Berisiko', 'items' => [
                'Berhenti merokok',
                'Batasi alkohol',
                'Kelola stres',
            ]],
            ['title' => 'Pantau Tekanan Darah', 'items' => [
                'Cek tekanan darah secara berkala',
            ]],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'intro' => 'Ada faktor risiko hipertensi yang perlu diperbaiki. Mulai dari pola makan dan aktivitas fisik.',
        'sections' => [
            ['title' => 'Ikuti Diet DASH', 'items' => [
                'Rendah garam',
                'Tinggi kalium dari buah dan sayur',
                'Kurangi lemak jenuh',
            ]],
            ['title' => 'Olahraga Teratur', 'items' => [
                'Minimal 30 menit setiap hari',
            ]],
            ['title' => 'Turunkan Berat Badan', 'items' => [
                'Targetkan penurunan 5–10% dari berat badan',
            ]],
            ['title' => 'Kelola Stres', 'items' => [
                'Relaksasi, meditasi, atau dzikir',
                'Istirahat cukup',
            ]],
            ['title' => 'Pantau Rutin', 'items' => [
                'Cek tekanan darah minimal sekali seminggu',
            ]],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi / Hipertensi Terdiagnosis',
        'intro' => 'Anda berisiko tinggi atau sudah terdiagnosis hipertensi. Kontrol rutin dan minum obat sesuai resep sangat penting.',
        'sections' => [
            ['title' => 'Kontrol Rutin ke Dokter', 'items' => [
                'Jangan lewatkan jadwal kontrol',
                'Evaluasi tekanan darah dan organ target',
            ]],
            ['title' => 'Minum Obat dengan Teratur', 'items' => [
                'Minum obat antihipertensi sesuai resep',
                'Jangan berhenti obat tanpa konsultasi dokter',
            ]],
            ['title' => 'Diet Ketat', 'items' => [
                'Garam kurang dari 5 gram per hari',
                'Hindari makanan tinggi natrium',
                'Perbanyak serat',
            ]],
            ['title' => 'Tetap Aktif', 'items' => [
                'Olahraga rutin sesuai kondisi tubuh',
            ]],
            ['title' => 'Kelola Stres', 'items' => [
                'Relaksasi, meditasi, atau dzikir',
            ]],
            ['title' => 'Pantau Mandiri', 'items' => [
                'Cek tekanan darah setiap hari',
                'Catat hasilnya',
            ]],
            ['title' => 'Dukungan Keluarga', 'items' => [
                'Minta keluarga mengingatkan minum obat',
                'Ajak keluarga ikut menjalani gaya hidup sehat',
            ]],
        ],
    ],
];
