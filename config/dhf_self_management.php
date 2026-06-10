<?php

return [
    'emergency' => [
        'title' => 'Segera ke IGD jika Anda mengalami:',
        'items' => [
            'Kulit pucat dan terasa dingin',
            'Nadi lemah namun cepat',
            'Nyeri perut yang sangat hebat',
            'Muntah terus-menerus',
            'Perdarahan dari hidung, gusi, atau muntah darah',
            'Lemas berat, gelisah, atau penurunan kesadaran',
        ],
    ],
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'intro' => 'Gejala Anda relatif ringan. Istirahat yang cukup dan cegah gigitan nyamuk tetap penting.',
        'sections' => [
            ['title' => 'Istirahat dan Minum Cukup', 'items' => [
                'Beristirahat sebanyak mungkin',
                'Minum banyak cairan seperti air putih, oralit, jus, atau sup',
            ]],
            ['title' => 'Atasi Demam dengan Aman', 'items' => [
                'Kompres hangat jika terasa tidak nyaman',
                'Paracetamol bisa membantu menurunkan demam sesuai dosis yang dianjurkan',
                'Hindari aspirin dan ibuprofen karena berisiko memperburuk perdarahan',
            ]],
            ['title' => 'Lindungi Diri dari Nyamuk', 'items' => [
                'Gunakan lotion anti nyamuk',
                'Tidur dengan kelambu',
                'Pakai pakaian yang menutup badan',
            ]],
            ['title' => 'Pantau Gejala Setiap Hari', 'items' => [
                'Catat suhu tubuh setiap hari',
                'Waspada jika muncul nyeri perut atau muntah',
            ]],
            ['title' => 'Jaga Lingkungan Bersih', 'items' => [
                'Lakukan 3M: menguras, menutup, dan mengubur tempat air',
                'Hilangkan genangan air di sekitar rumah',
            ]],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'intro' => 'Kondisi perlu dipantau lebih ketat. Segera konsultasikan ke tenaga kesehatan.',
        'sections' => [
            ['title' => 'Konsultasi ke Tenaga Kesehatan', 'items' => [
                'Periksa ke puskesmas atau klinik',
                'Minta pemeriksaan darah (trombosit dan hematokrit) jika diperlukan',
            ]],
            ['title' => 'Perbanyak Asupan Cairan', 'items' => [
                'Dewasa disarankan minum 2–3 liter per hari',
                'Oralit sangat dianjurkan',
                'Boleh minum air putih, jus, sup, atau air kelapa',
            ]],
            ['title' => 'Pantau Kondisi Setiap Hari', 'items' => [
                'Suhu tubuh',
                'Seberapa sering muntah',
                'Nafsu makan',
                'Jumlah urine yang keluar',
            ]],
            ['title' => 'Waspadai Hari ke-3 sampai ke-7', 'items' => [
                'Fase ini sering menjadi masa kritis',
                'Meskipun demam turun, kondisi belum tentu sudah aman',
                'Segera ke fasilitas kesehatan jika kondisi memburuk',
            ]],
            ['title' => 'Cegah Penularan', 'items' => [
                'Tetap gunakan kelambu dan obat anti nyamuk',
                'Hindari digigit nyamuk agar virus tidak menular ke orang lain',
            ]],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi',
        'intro' => 'Kondisi berisiko berat. Istirahat total dan segera ke fasilitas kesehatan dengan pendamping keluarga.',
        'sections' => [
            ['title' => 'Istirahat Total', 'items' => [
                'Berbaring dan hindari aktivitas berat',
                'Jika sesak, tidur dengan posisi setengah duduk yang nyaman',
            ]],
            ['title' => 'Cairan untuk Tubuh', 'items' => [
                'Minum jika masih sanggup menelan',
                'Jangan dipaksa minum jika muntah sangat berat',
            ]],
            ['title' => 'Hindari Obat yang Berbahaya', 'items' => [
                'Jangan minum aspirin',
                'Jangan minum ibuprofen',
                'Hindari obat antiinflamasi lain tanpa anjuran dokter',
            ]],
            ['title' => 'Peran Keluarga/Pendamping', 'items' => [
                'Pantau tanda syok seperti tangan dan kaki dingin',
                'Perhatikan nadi yang cepat',
                'Perhatikan tekanan darah yang turun',
                'Segera bawa ke IGD jika tanda syok muncul',
            ]],
        ],
    ],
];
