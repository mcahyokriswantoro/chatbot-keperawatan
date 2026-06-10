<?php

return [
    'emergency' => [
        'title' => 'Segera ke rumah sakit jika Anda mengalami:',
        'items' => [
            'Gula darah sangat tinggi (lebih dari 300 mg/dL)',
            'Penurunan kesadaran atau pingsan',
            'Luka yang tidak sembuh',
            'Sesak napas atau mual muntah berat',
        ],
    ],
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'intro' => 'Risiko diabetes Anda relatif rendah. Jaga pola hidup sehat agar tetap terkendali.',
        'sections' => [
            ['title' => 'Makan dengan Bijak', 'items' => [
                'Kurangi minuman manis, kue, dan gula sederhana',
                'Perbanyak serat dari sayur, buah, dan biji-bijian',
                'Pilih karbohidrat kompleks seperti nasi merah atau oat',
            ]],
            ['title' => 'Aktif Bergerak', 'items' => [
                'Olahraga minimal 150 menit per minggu',
                'Jalan cepat, bersepeda, atau senam',
            ]],
            ['title' => 'Jaga Berat Badan', 'items' => [
                'Usahakan berat badan tetap ideal (IMT normal)',
            ]],
            ['title' => 'Hindari Kebiasaan Berisiko', 'items' => [
                'Berhenti merokok',
                'Kelola stres dengan baik',
            ]],
            ['title' => 'Pantau Gula Darah', 'items' => [
                'Cek gula darah secara berkala',
            ]],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'intro' => 'Ada tanda yang perlu diwaspadai. Perbaiki pola makan dan konsultasikan ke tenaga kesehatan.',
        'sections' => [
            ['title' => 'Atur Pola Makan Ketat', 'items' => [
                'Kurangi karbohidrat sederhana',
                'Atur porsi: 3 kali makan utama dan 2 kali snack sehat',
                'Hindari minuman manis',
            ]],
            ['title' => 'Olahraga Teratur', 'items' => [
                'Minimal 30 menit setiap hari',
                'Kombinasikan aerobik dan latihan kekuatan ringan',
            ]],
            ['title' => 'Turunkan Berat Badan', 'items' => [
                'Targetkan penurunan 5–10% dari berat badan saat ini',
            ]],
            ['title' => 'Periksa Secara Rutin', 'items' => [
                'Gula darah puasa',
                'HbA1c',
                'Tekanan darah',
            ]],
            ['title' => 'Kenali Gejala Diabetes', 'items' => [
                'Pelajari tanda-tanda diabetes',
                'Konsultasikan ke tenaga kesehatan jika ada keluhan',
            ]],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi / Diabetes',
        'intro' => 'Risiko diabetes tinggi atau Anda sudah terdiagnosis diabetes. Kontrol rutin dan kepatuhan pengobatan sangat penting.',
        'sections' => [
            ['title' => 'Kontrol Rutin ke Dokter', 'items' => [
                'Jangan lewatkan jadwal kontrol',
                'Periksa gula darah dan HbA1c secara berkala',
            ]],
            ['title' => 'Minum Obat dengan Teratur', 'items' => [
                'Minum obat antidiabetes atau insulin sesuai resep',
                'Jangan berhenti obat tanpa konsultasi dokter',
            ]],
            ['title' => 'Diet untuk Diabetes', 'items' => [
                'Kurangi gula dan makanan manis',
                'Atur jumlah karbohidrat',
                'Perbanyak serat',
                'Hindari makanan dengan indeks glikemik tinggi',
            ]],
            ['title' => 'Tetap Aktif', 'items' => [
                'Olahraga rutin sesuai kemampuan',
                'Hindari duduk terlalu lama tanpa bergerak',
            ]],
            ['title' => 'Pantau Mandiri', 'items' => [
                'Cek gula darah di rumah jika punya alat',
                'Catat hasil setiap hari',
            ]],
            ['title' => 'Rawat Kaki dengan Baik', 'items' => [
                'Periksa kaki setiap hari',
                'Jaga kaki tetap bersih dan hindari luka',
                'Gunakan alas kaki yang nyaman',
            ]],
            ['title' => 'Dukungan Keluarga', 'items' => [
                'Minta keluarga membantu mengawasi pola makan',
                'Minta pengingat minum obat',
            ]],
        ],
    ],
];
