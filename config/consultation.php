<?php

return [

    'whatsapp' => [
        'api_base' => env('CONSULTATION_WA_API', 'https://api.whatsapp.com/send'),
    ],

    /*
    | Notifikasi WA ke tenaga kesehatan saat pasien kirim pesan.
    | driver: log (dev) | fonnte | wablas | disabled
    */
    'notification' => [
        'driver' => env('CONSULTATION_WA_NOTIFY_DRIVER', 'log'),
        'fonnte_token' => env('FONNTE_TOKEN'),
        'wablas_token' => env('WABLAS_TOKEN'),
        'wablas_secret' => env('WABLAS_SECRET'),
    ],

    'pricing' => [
        'default' => (int) env('CONSULTATION_PRICE', 25000),
        'perawat' => (int) env('CONSULTATION_PRICE_PERAWAT', 25000),
        'dokter_umum' => (int) env('CONSULTATION_PRICE_DOKTER_UMUM', 50000),
        'dokter_spesialis' => (int) env('CONSULTATION_PRICE_DOKTER_SPESIALIS', 75000),
    ],

    'session_hours' => (int) env('CONSULTATION_SESSION_HOURS', 24),

    'dana' => [
        'merchant_name' => env('CONSULTATION_DANA_MERCHANT', 'Chatbot Keperawatan'),
        'merchant_phone' => env('CONSULTATION_DANA_PHONE', '085645527751'),
    ],

    'demo_vouchers' => [
        ['code' => 'PERAWAT100', 'discount_percent' => 100, 'provider_key' => 'perawat', 'max_uses' => 100],
        ['code' => 'GRATIS100', 'discount_percent' => 100, 'provider_key' => null, 'max_uses' => 50],
    ],

    'categories' => [
        [
            'key' => 'perawat',
            'label' => 'Perawat (Ners)',
            'icon' => '👩‍⚕️',
            'description' => 'Edukasi perawatan, pantau gejala, dan bantuan self management di rumah.',
            'active' => true,
            'primary' => true,
        ],
        [
            'key' => 'dokter_umum',
            'label' => 'Dokter Umum',
            'icon' => '👨‍⚕️',
            'description' => 'Konsultasi keluhan umum, interpretasi hasil skrining, dan rujukan lanjut.',
            'active' => false,
            'primary' => true,
        ],
        [
            'key' => 'dokter_spesialis',
            'label' => 'Dokter Spesialis',
            'icon' => '🩺',
            'description' => 'Penanganan lebih spesifik sesuai bidang medis terkait kondisi Anda.',
            'active' => false,
            'primary' => true,
        ],
        [
            'key' => 'penyakit_dalam',
            'label' => 'Spesialis Penyakit Dalam',
            'icon' => '🫀',
            'description' => 'Konsultasi gangguan metabolik, diabetes, hipertensi, dan lainnya.',
            'active' => false,
            'parent_key' => 'dokter_spesialis',
        ],
        [
            'key' => 'kandungan',
            'label' => 'Spesialis Kandungan',
            'icon' => '🤰',
            'description' => 'Kesehatan ibu hamil, kehamilan, dan reproduksi.',
            'active' => false,
            'parent_key' => 'dokter_spesialis',
        ],
        [
            'key' => 'anak',
            'label' => 'Spesialis Anak',
            'icon' => '👶',
            'description' => 'Kesehatan dan tumbuh kembang anak.',
            'active' => false,
            'parent_key' => 'dokter_spesialis',
        ],
        [
            'key' => 'jiwa',
            'label' => 'Kesehatan Jiwa',
            'icon' => '🧠',
            'description' => 'Konsultasi kesehatan mental dan dukungan psikologis.',
            'active' => false,
            'parent_key' => 'dokter_spesialis',
        ],
        [
            'key' => 'gizi',
            'label' => 'Spesialis Gizi',
            'icon' => '🥗',
            'description' => 'Pola makan sehat dan nutrisi terapeutik.',
            'active' => false,
            'parent_key' => 'dokter_spesialis',
        ],
    ],

    'providers' => [
        'perawat' => [
            'active' => true,
            'category' => 'perawat',
            'name' => 'Abdul Aziz Alimul Hidayat, S.Kep., N.s',
            'short_name' => 'Abdul Aziz Alimul Hidayat',
            'title' => 'Perawat Profesional',
            'specialty' => 'Perawat (Ners)',
            'credential' => 'S.Kep., N.s',
            'experience_years' => 8,
            'rating_percent' => 100,
            'photo' => 'images/avatars/male.svg',
            'icon' => '👩‍⚕️',
            'whatsapp' => '085645527751',
            'whatsapp_intl' => '6285645527751',
            'greeting' => 'Halo! Saya Abdul Aziz, perawat di Chatbot Keperawatan. Silakan ceritakan keluhan atau pertanyaan seputar perawatan di rumah. Saya akan membalas lewat chat ini.',
            'auto_reply' => 'Terima kasih atas pesan Anda. Tim kesehatan akan segera membalas di chat ini.',
        ],
    ],

];
