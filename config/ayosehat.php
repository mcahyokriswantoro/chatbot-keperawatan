<?php

return [
    'base_url' => 'https://ayosehat.kemkes.go.id/',

    /*
    | Kategori yang disinkronkan otomatis dari /category/{slug}
    | Jalankan: php artisan ayosehat:sync
    */
    'categories' => [
        [
            'id' => 'cegah',
            'slug' => 'cegah',
            'name' => 'Cegah',
            'icon' => '🛡️',
            'description' => 'Pencegahan penyakit & mitos kesehatan',
            'gradient' => 'from-emerald-500 to-teal-600',
            'chip' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        ],
        [
            'id' => 'deteksi',
            'slug' => 'deteksi',
            'name' => 'Deteksi',
            'icon' => '🔍',
            'description' => 'Deteksi dini & pemeriksaan kesehatan',
            'gradient' => 'from-brand-500 to-blue-600',
            'chip' => 'bg-brand-50 text-brand-700 ring-brand-200',
        ],
        [
            'id' => 'pengobatan',
            'slug' => 'pengobatan',
            'name' => 'Pengobatan',
            'icon' => '💊',
            'description' => 'Terapi, pengobatan & perawatan',
            'gradient' => 'from-violet-500 to-purple-600',
            'chip' => 'bg-violet-50 text-violet-700 ring-violet-200',
        ],
    ],
];
