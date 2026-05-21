<?php

/**
 * Panduan self-management PPOK berdasarkan klasifikasi risiko skrining.
 */
return [
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'sections' => [
            [
                'title' => 'Berhenti & Hindari Rokok',
                'items' => [
                    'Tidak merokok',
                    'Hindari asap rokok (perokok pasif)',
                ],
            ],
            [
                'title' => 'Lindungi dari Polusi',
                'items' => [
                    'Gunakan masker di lingkungan berdebu/polusi',
                    'Kurangi paparan asap dapur (biomassa)',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik',
                'items' => [
                    'Olahraga ringan: jalan kaki 20–30 menit/hari',
                    'Latihan pernapasan sederhana',
                ],
            ],
            [
                'title' => 'Beri Nutrisi Seimbang',
                'items' => [
                    'Konsumsi makanan tinggi protein & antioksidan',
                    'Cukup minum air',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring',
                'items' => [
                    'Skrining ulang bila muncul batuk kronis / sesak',
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
                    'Evaluasi fungsi paru (spirometri jika tersedia)',
                ],
            ],
            [
                'title' => 'Lakukan Latihan Pernapasan',
                'items' => [
                    'Pursed-lip breathing (tarik napas hidung, hembuskan perlahan lewat mulut)',
                    'Diaphragmatic breathing',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik Terukur',
                'items' => [
                    'Jalan kaki rutin',
                    'Hindari aktivitas berat berlebihan',
                    'Program rehabilitasi paru (jika tersedia)',
                ],
            ],
            [
                'title' => 'Lakukan Manajemen Dahak',
                'items' => [
                    'Minum cukup cairan',
                    'Teknik batuk efektif',
                ],
            ],
            [
                'title' => 'Hindari Pencetus',
                'items' => [
                    'Asap rokok',
                    'Debu dan polusi',
                    'Udara dingin ekstrem',
                ],
            ],
            [
                'title' => 'Lakukan Vaksinasi',
                'items' => [
                    'Influenza',
                    'Pneumonia',
                ],
            ],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi',
        'sections' => [
            [
                'title' => 'Lakukan Tindakan Utama',
                'items' => [
                    'Wajib kontrol rutin ke dokter',
                    'Evaluasi fungsi paru (spirometri)',
                    'Terapi inhaler sesuai resep',
                ],
            ],
            [
                'title' => 'Lakukan Kepatuhan Terapi',
                'items' => [
                    'Gunakan inhaler secara teratur (bronkodilator/kortikosteroid)',
                    'Jangan menghentikan obat tanpa konsultasi',
                ],
            ],
            [
                'title' => 'Lakukan Rehabilitasi Paru',
                'items' => [
                    'Latihan napas intensif',
                    'Latihan fisik terstruktur',
                    'Edukasi teknik energi konservasi',
                ],
            ],
            [
                'title' => 'Lakukan Manajemen Aktivitas',
                'items' => [
                    'Istirahat cukup',
                    'Gunakan teknik “hemat energi” saat beraktivitas',
                ],
            ],
            [
                'title' => 'Beri Nutrisi',
                'items' => [
                    'Tinggi protein',
                    'Cukup kalori',
                    'Pantau berat badan (hindari malnutrisi)',
                ],
            ],
            [
                'title' => 'Lakukan Pencegahan Infeksi',
                'items' => [
                    'Gunakan masker',
                    'Cuci tangan rutin',
                    'Hindari kerumunan saat sakit',
                ],
            ],
            [
                'title' => 'Beri Dukungan Keluarga',
                'items' => [
                    'Edukasi keluarga tentang PPOK',
                    'Bantuan aktivitas harian jika diperlukan',
                ],
            ],
        ],
    ],
];
