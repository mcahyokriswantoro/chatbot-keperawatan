<?php

/**
 * Panduan self-management DHF berdasarkan klasifikasi risiko skrining.
 */
return [
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'sections' => [
            [
                'title' => 'Lakukan Istirahat & Hidrasi',
                'items' => [
                    'Istirahat cukup',
                    'Minum banyak cairan (air putih, oralit, jus, sup)',
                ],
            ],
            [
                'title' => 'Lakukan Penanganan Demam',
                'items' => [
                    'Kompres hangat',
                    'Obat penurun demam (parasetamol sesuai dosis)',
                    'Hindari aspirin/ibuprofen (risiko perdarahan)',
                ],
            ],
            [
                'title' => 'Lakukan Pencegahan Gigitan Nyamuk',
                'items' => [
                    'Gunakan lotion anti nyamuk',
                    'Tidur menggunakan kelambu',
                    'Gunakan pakaian tertutup',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring Gejala',
                'items' => [
                    'Pantau suhu tubuh harian',
                    'Waspadai munculnya gejala baru (nyeri perut, muntah)',
                ],
            ],
            [
                'title' => 'Jaga Lingkungan',
                'items' => [
                    'Lakukan 3M (Menguras, Menutup, Mengubur)',
                    'Hilangkan tempat perkembangbiakan nyamuk',
                ],
            ],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'sections' => [
            [
                'title' => 'Lakukan Konsultasi Medis',
                'items' => [
                    'Periksa ke puskesmas/klinik',
                    'Pemeriksaan darah (trombosit, hematokrit)',
                ],
            ],
            [
                'title' => 'Lakukan Hidrasi Intensif',
                'items' => [
                    'Minum 2–3 liter/hari (dewasa)',
                    'Oralit sangat dianjurkan',
                    'Cairan: air, jus, sup, air kelapa',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring Ketat',
                'items' => [
                    'Pantau setiap hari: suhu tubuh',
                    'Frekuensi muntah',
                    'Nafsu makan',
                    'Jumlah urin',
                ],
            ],
            [
                'title' => 'Waspadai Fase Kritis (Hari 3–7)',
                'items' => [
                    'Perhatikan bila demam turun tetapi kondisi tidak membaik',
                    'Segera ke fasilitas kesehatan bila gejala memberat',
                ],
            ],
            [
                'title' => 'Lakukan Pencegahan Penularan',
                'items' => [
                    'Tetap gunakan kelambu / anti nyamuk',
                    'Hindari gigitan nyamuk (agar tidak menularkan ke orang lain)',
                ],
            ],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi',
        'sections' => [
            [
                'title' => 'Atur Posisi',
                'items' => [
                    'Istirahat total (bed rest)',
                    'Posisi nyaman (semi-fowler jika sesak)',
                ],
            ],
            [
                'title' => 'Beri Cairan',
                'items' => [
                    'Minum jika masih bisa',
                    'Jangan dipaksa jika muntah berat',
                ],
            ],
            [
                'title' => 'Jangan Minum Obat Sembarangan',
                'items' => [
                    'Hindari aspirin',
                    'Hindari ibuprofen',
                    'Hindari obat antiinflamasi lain',
                ],
            ],
            [
                'title' => 'Peran Keluarga',
                'items' => [
                    'Pantau tanda syok: tangan/kaki dingin',
                    'Nadi cepat',
                    'Tekanan darah turun',
                    'Segera bawa ke fasilitas kesehatan/IGD bila tanda syok muncul',
                ],
            ],
        ],
    ],
];
