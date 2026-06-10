<?php

return [
    'emergency' => [
        'title' => 'Segera ke rumah sakit jika Anda mengalami:',
        'items' => [
            'Nyeri dada lebih dari 20 menit',
            'Nyeri menjalar ke lengan atau leher',
            'Berkeringat dingin',
            'Sesak napas',
        ],
    ],
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'intro' => 'Jantung Anda relatif baik. Pertahankan gaya hidup sehat untuk mencegah penyakit jantung koroner.',
        'sections' => [
            ['title' => 'Makan untuk Jantung Sehat', 'items' => [
                'Kurangi lemak jenuh dan makanan gorengan',
                'Batasi garam kurang dari 5 gram per hari',
                'Perbanyak buah, sayur, dan serat',
            ]],
            ['title' => 'Aktif Bergerak', 'items' => [
                'Olahraga minimal 150 menit per minggu',
                'Jalan cepat, bersepeda, atau senam ringan',
            ]],
            ['title' => 'Hindari Kebiasaan Berisiko', 'items' => [
                'Berhenti merokok',
                'Batasi alkohol',
                'Kelola stres',
            ]],
            ['title' => 'Pantau Kesehatan', 'items' => [
                'Cek tekanan darah, gula darah, dan kolesterol secara berkala',
            ]],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'intro' => 'Ada faktor risiko yang perlu dikendalikan. Konsultasikan ke tenaga kesehatan untuk evaluasi lebih lanjut.',
        'sections' => [
            ['title' => 'Kendalikan Faktor Risiko', 'items' => [
                'Jaga tekanan darah tetap terkontrol',
                'Kendalikan diabetes jika ada',
                'Turunkan kolesterol',
            ]],
            ['title' => 'Diet Jantung Sehat', 'items' => [
                'Ikuti pola makan DASH atau Mediterania',
                'Kurangi lemak jenuh dan lemak trans',
                'Batasi gula dan garam',
            ]],
            ['title' => 'Olahraga Rutin', 'items' => [
                'Olahraga ringan sampai sedang 30 menit setiap hari',
            ]],
            ['title' => 'Kepatuhan Obat', 'items' => [
                'Minum obat sesuai resep jika sudah diresepkan dokter',
            ]],
            ['title' => 'Pantau Berkala', 'items' => [
                'Tekanan darah',
                'Gula darah',
                'Kolesterol',
            ]],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi',
        'intro' => 'Risiko penyakit jantung koroner tinggi. Kontrol rutin dan patuhi terapi sangat penting.',
        'sections' => [
            ['title' => 'Kontrol Rutin ke Dokter', 'items' => [
                'Jangan lewatkan jadwal kontrol',
                'Lakukan pemeriksaan jantung seperti EKG dan lab sesuai anjuran',
            ]],
            ['title' => 'Patuhi Pengobatan', 'items' => [
                'Minum obat antihipertensi jika diresepkan',
                'Minum statin untuk menurunkan kolesterol jika diresepkan',
                'Minum obat antiplatelet jika diresepkan',
                'Jangan berhenti obat tanpa konsultasi dokter',
            ]],
            ['title' => 'Atur Aktivitas', 'items' => [
                'Lakukan aktivitas ringan yang terkontrol',
                'Hindari aktivitas berat secara mendadak',
            ]],
            ['title' => 'Kenali dan Atasi Gejala', 'items' => [
                'Beristirahat saat nyeri dada muncul',
                'Gunakan obat sesuai anjuran dokter',
            ]],
            ['title' => 'Diet Ketat', 'items' => [
                'Rendah garam',
                'Rendah lemak jenuh',
                'Tinggi serat',
            ]],
            ['title' => 'Dukungan Keluarga', 'items' => [
                'Ajak keluarga memahami kondisi jantung Anda',
                'Minta bantuan mengingatkan obat',
                'Minta pendamping saat beraktivitas jika diperlukan',
            ]],
        ],
    ],
];
