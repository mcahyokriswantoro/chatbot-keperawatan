<?php

/**
 * Panduan self-management Penyakit Ginjal berdasarkan klasifikasi risiko skrining.
 */
return [
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'sections' => [
            [
                'title' => 'Lakukan Hidrasi Cukup',
                'items' => [
                    'Minum ± 1,5–2 liter/hari (sesuai kondisi)',
                    'Hindari dehidrasi',
                ],
            ],
            [
                'title' => 'Lakukan Pola Makan Sehat',
                'items' => [
                    'Kurangi garam (< 5 gram/hari)',
                    'Batasi makanan tinggi lemak & gula',
                    'Perbanyak sayur dan buah',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik',
                'items' => [
                    'Olahraga ringan 30 menit/hari',
                ],
            ],
            [
                'title' => 'Hindari Obat Berisiko',
                'items' => [
                    'Hindari penggunaan jangka panjang obat nyeri (NSAID) tanpa pengawasan',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring',
                'items' => [
                    'Cek tekanan darah & gula darah berkala',
                    'Skrining ulang bila ada gejala',
                ],
            ],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'sections' => [
            [
                'title' => 'Lakukan Kontrol Penyakit Penyerta',
                'items' => [
                    'Kendalikan Diabetes Mellitus',
                    'Kendalikan Hipertensi',
                ],
            ],
            [
                'title' => 'Lakukan Pola Diet Ginjal Sehat',
                'items' => [
                    'Kurangi garam (maksimal 5 gram/hari)',
                    'Batasi protein berlebihan',
                    'Hindari makanan tinggi natrium (makanan instan)',
                ],
            ],
            [
                'title' => 'Lakukan Hidrasi Terukur',
                'items' => [
                    'Minum cukup (tidak berlebihan)',
                    'Sesuaikan dengan kondisi tubuh',
                ],
            ],
            [
                'title' => 'Lakukan Pemeriksaan Rutin',
                'items' => [
                    'Urinalisis (protein urin)',
                    'Kreatinin dan eGFR',
                    'Tekanan darah',
                ],
            ],
            [
                'title' => 'Lakukan Gaya Hidup',
                'items' => [
                    'Berhenti merokok',
                    'Kurangi alkohol',
                    'Kelola stres',
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
                    'Wajib kontrol ke dokter secara rutin',
                    'Evaluasi fungsi ginjal (eGFR, kreatinin, urin)',
                ],
            ],
            [
                'title' => 'Lakukan Kepatuhan Terapi',
                'items' => [
                    'Minum obat sesuai resep (antihipertensi, antidiabetes)',
                    'Jangan konsumsi obat tanpa konsultasi',
                ],
            ],
            [
                'title' => 'Lakukan Diet Khusus Ginjal',
                'items' => [
                    'Batasi protein (sesuai anjuran dokter)',
                    'Kurangi garam & kalium (jika diperlukan)',
                    'Batasi fosfor (makanan olahan, soda)',
                ],
            ],
            [
                'title' => 'Lakukan Manajemen Cairan',
                'items' => [
                    'Batasi cairan jika ada pembengkakan',
                    'Ikuti anjuran dokter',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring Ketat',
                'items' => [
                    'Berat badan harian',
                    'Pembengkakan (edema)',
                    'Jumlah urin',
                ],
            ],
            [
                'title' => 'Beri Dukungan Keluarga',
                'items' => [
                    'Edukasi keluarga',
                    'Bantuan kontrol diet dan obat',
                ],
            ],
            [
                'title' => 'Lakukan Persiapan Lanjutan (Jika Diperlukan)',
                'items' => [
                    'Edukasi tentang dialisis (cuci darah)',
                    'Konsultasi spesialis ginjal',
                ],
            ],
        ],
    ],
];
