<?php

return [
    'emergency' => [
        'title' => 'Segera ke rumah sakit jika Anda mengalami:',
        'items' => [
            'Bengkak berat di wajah atau kaki',
            'Sesak napas',
            'Jumlah urine menurun drastis',
            'Mual dan muntah yang sangat berat',
        ],
    ],
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'intro' => 'Fungsi ginjal Anda relatif baik. Jaga pola hidup sehat agar tetap terjaga.',
        'sections' => [
            ['title' => 'Minum Air yang Cukup', 'items' => [
                'Usahakan minum sekitar 1,5–2 liter per hari, sesuai kondisi tubuh',
                'Hindari kekurangan cairan (dehidrasi)',
            ]],
            ['title' => 'Makan dengan Seimbang', 'items' => [
                'Batasi garam kurang dari 5 gram per hari',
                'Kurangi makanan tinggi lemak dan gula',
                'Perbanyak sayur dan buah',
            ]],
            ['title' => 'Aktif Bergerak', 'items' => [
                'Olahraga ringan sekitar 30 menit setiap hari',
            ]],
            ['title' => 'Hati-hati dengan Obat', 'items' => [
                'Hindari obat pereda nyeri (NSAID) jangka panjang tanpa pengawasan dokter',
            ]],
            ['title' => 'Pantau Kesehatan', 'items' => [
                'Cek tekanan darah dan gula darah secara berkala',
                'Ulangi skrining jika muncul gejala baru',
            ]],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'intro' => 'Ginjal perlu perhatian lebih. Kendalikan penyakit penyerta dan periksa rutin ke tenaga kesehatan.',
        'sections' => [
            ['title' => 'Kendalikan Penyakit Penyerta', 'items' => [
                'Jaga gula darah jika Anda punya diabetes',
                'Jaga tekanan darah tetap terkontrol',
            ]],
            ['title' => 'Diet Sehat untuk Ginjal', 'items' => [
                'Batasi garam maksimal 5 gram per hari',
                'Jangan berlebihan mengonsumsi protein',
                'Kurangi makanan instan yang tinggi natrium',
            ]],
            ['title' => 'Atur Asupan Cairan', 'items' => [
                'Minum cukup, tetapi tidak berlebihan',
                'Sesuaikan dengan kondisi dan anjuran dokter',
            ]],
            ['title' => 'Periksa Secara Rutin', 'items' => [
                'Urinalisis untuk memeriksa protein urin',
                'Kreatinin dan eGFR',
                'Tekanan darah',
            ]],
            ['title' => 'Gaya Hidup Sehat', 'items' => [
                'Berhenti merokok',
                'Batasi alkohol',
                'Kelola stres dengan baik',
            ]],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi',
        'intro' => 'Fungsi ginjal perlu penanganan medis ketat. Patuhi terapi dan diet sesuai anjuran dokter.',
        'sections' => [
            ['title' => 'Kontrol Rutin ke Dokter', 'items' => [
                'Jangan lewatkan jadwal kontrol',
                'Evaluasi fungsi ginjal (eGFR, kreatinin, urin)',
            ]],
            ['title' => 'Minum Obat Sesuai Resep', 'items' => [
                'Minum obat antihipertensi atau antidiabetes jika diresepkan',
                'Jangan minum obat sendiri tanpa konsultasi',
            ]],
            ['title' => 'Diet Khusus Ginjal', 'items' => [
                'Batasi protein sesuai anjuran dokter',
                'Kurangi garam dan kalium jika diperlukan',
                'Batasi fosfor dari makanan olahan dan minuman soda',
            ]],
            ['title' => 'Kelola Asupan Cairan', 'items' => [
                'Batasi cairan jika ada pembengkakan',
                'Ikuti petunjuk dokter tentang jumlah minum per hari',
            ]],
            ['title' => 'Pantau Setiap Hari', 'items' => [
                'Timbang berat badan setiap hari',
                'Perhatikan pembengkakan di tubuh',
                'Catat jumlah urine yang keluar',
            ]],
            ['title' => 'Dukungan Keluarga', 'items' => [
                'Ajak keluarga memahami kondisi ginjal Anda',
                'Minta bantuan mengatur diet dan pengingat obat',
            ]],
            ['title' => 'Persiapan Jangka Panjang', 'items' => [
                'Pelajari tentang dialisis (cuci darah) jika diperlukan',
                'Konsultasi dengan dokter spesialis ginjal',
            ]],
        ],
    ],
];
