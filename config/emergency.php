<?php

return [
    'hotlines' => [
        [
            'name' => 'Ambulans / PSC 119',
            'number' => '119',
            'description' => 'Darurat medis nasional · 24 jam',
            'icon' => '🚑',
            'category' => 'medis',
        ],
        [
            'name' => 'PMI',
            'number' => '115',
            'description' => 'Palang Merah Indonesia',
            'icon' => '🩸',
            'category' => 'medis',
        ],
        [
            'name' => 'Kemenkes Halo',
            'number' => '1500567',
            'description' => 'Konsultasi kesehatan 24 jam',
            'icon' => '📞',
            'category' => 'konsultasi',
        ],
        [
            'name' => 'Polisi',
            'number' => '110',
            'description' => 'Keamanan & bantuan darurat',
            'icon' => '🚔',
            'category' => 'keamanan',
        ],
    ],

    'categories' => [
        ['id' => 'all', 'label' => 'Semua', 'icon' => '📋'],
        ['id' => 'medis', 'label' => 'Medis', 'icon' => '🏥'],
        ['id' => 'konsultasi', 'label' => 'Konsultasi', 'icon' => '💬'],
        ['id' => 'keamanan', 'label' => 'Keamanan', 'icon' => '🛡️'],
    ],

    'symptoms' => [
        ['id' => 'breath', 'label' => 'Sesak napas berat / tidak bisa bernapas', 'icon' => '🫁'],
        ['id' => 'chest', 'label' => 'Nyeri dada hebat atau tertusuk', 'icon' => '💔'],
        ['id' => 'conscious', 'label' => 'Penurunan kesadaran / pingsan', 'icon' => '😵'],
        ['id' => 'bleeding', 'label' => 'Perdarahan hebat tidak terkontrol', 'icon' => '🩸'],
        ['id' => 'stroke', 'label' => 'Wajah mencong, lengan lemah, bicara pelo (FAST)', 'icon' => '🧠'],
        ['id' => 'seizure', 'label' => 'Kejang berulang / tidak berhenti', 'icon' => '⚡'],
        ['id' => 'allergy', 'label' => 'Reaksi alergi berat (bengkak wajah, sesak)', 'icon' => '🤧'],
        ['id' => 'injury', 'label' => 'Cedera berat / kecelakaan', 'icon' => '🦴'],
    ],

    'fast' => [
        ['key' => 'F', 'title' => 'Face — Wajah', 'desc' => 'Minta orang tersebut tersenyum. Apakah satu sisi wajah tampak mencong atau turun?'],
        ['key' => 'A', 'title' => 'Arms — Lengan', 'desc' => 'Minta angkat kedua lengan. Apakah satu lengan lemah, turun, atau tidak bisa diangkat?'],
        ['key' => 'S', 'title' => 'Speech — Bicara', 'desc' => 'Minta ulang kalimat sederhana. Apakah bicaranya pelo, tidak jelas, atau tidak masuk akal?'],
        ['key' => 'T', 'title' => 'Time — Waktu', 'desc' => 'Jika ada satu tanda saja, catat waktu mulai gejala dan SEGERA ke IGD / hubungi 119.'],
    ],

    'steps' => [
        ['icon' => '📞', 'title' => 'Hubungi 119', 'desc' => 'Jelaskan lokasi, kondisi pasien, dan gejala utama dengan tenang.'],
        ['icon' => '🧘', 'title' => 'Tetap tenang', 'desc' => 'Jangan tinggalkan pasien sendirian. Pastikan jalan napas terbuka.'],
        ['icon' => '🏥', 'title' => 'Ke IGD terdekat', 'desc' => 'Jangan menunggu skrining aplikasi selesai. Waktu adalah kunci penanganan.'],
        ['icon' => '📋', 'title' => 'Bawa informasi', 'desc' => 'Catat obat rutin, alergi, dan riwayat penyakit jika memungkinkan.'],
    ],

    'warnings' => [
        [
            'title' => 'Kapan harus ke IGD?',
            'body' => 'Segera hubungi layanan darurat jika mengalami sesak napas berat, nyeri dada hebat, penurunan kesadaran, gejala stroke (FAST), perdarahan hebat, atau kejang tidak berhenti.',
        ],
        [
            'title' => 'Batasan aplikasi',
            'body' => 'Hasil skrining chatbot keperawatan bersifat informatif untuk deteksi dini — bukan diagnosis medis. Keputusan penanganan darurat harus dari tenaga kesehatan.',
        ],
        [
            'title' => 'Jangan menunda',
            'body' => 'Gejala yang memberat dalam 24 jam memerlukan pemeriksaan profesional. Lebih baik diperiksa dan ternyata ringan, daripada menunda dan berisiko fatal.',
        ],
    ],
];
