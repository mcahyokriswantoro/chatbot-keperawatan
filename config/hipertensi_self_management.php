<?php

/**
 * Panduan self-management Hipertensi berdasarkan klasifikasi risiko skrining.
 */
return [
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'sections' => [
            [
                'title' => 'Jaga Pola Makan Sehat',
                'items' => [
                    'Batasi garam (< 5 gram/hari)',
                    'Perbanyak buah dan sayur',
                    'Kurangi makanan olahan/instan',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik',
                'items' => [
                    'Olahraga ≥ 150 menit/minggu',
                    'Jalan cepat, bersepeda, senam',
                ],
            ],
            [
                'title' => 'Jaga Berat Badan Ideal',
                'items' => [
                    'Pertahankan IMT normal',
                ],
            ],
            [
                'title' => 'Hindari Faktor Risiko',
                'items' => [
                    'Tidak merokok',
                    'Batasi alkohol',
                    'Kelola stres',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring',
                'items' => [
                    'Cek tekanan darah secara berkala',
                ],
            ],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'sections' => [
            [
                'title' => 'Lakukan Diet DASH (Dietary Approaches to Stop Hypertension)',
                'items' => [
                    'Rendah garam',
                    'Tinggi kalium (buah & sayur)',
                    'Kurangi lemak jenuh',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik Teratur',
                'items' => [
                    'Minimal 30 menit/hari',
                ],
            ],
            [
                'title' => 'Lakukan Penurunan Berat Badan',
                'items' => [
                    'Target penurunan 5–10%',
                ],
            ],
            [
                'title' => 'Lakukan Manajemen Stres',
                'items' => [
                    'Relaksasi, meditasi, dzikir',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring Rutin',
                'items' => [
                    'Cek tekanan darah minimal 1x/minggu',
                ],
            ],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi / Hipertensi Terdiagnosis',
        'sections' => [
            [
                'title' => 'Lakukan Tindakan Utama',
                'items' => [
                    'Wajib kontrol rutin ke dokter',
                    'Evaluasi tekanan darah dan organ target',
                ],
            ],
            [
                'title' => 'Lakukan Kepatuhan Terapi',
                'items' => [
                    'Minum obat antihipertensi sesuai resep',
                    'Tidak menghentikan obat tanpa konsultasi',
                ],
            ],
            [
                'title' => 'Lakukan Diet Ketat',
                'items' => [
                    'Garam < 5 gram/hari',
                    'Hindari makanan tinggi natrium',
                    'Perbanyak serat',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik',
                'items' => [
                    'Olahraga rutin (sesuai kondisi)',
                ],
            ],
            [
                'title' => 'Lakukan Manajemen Stres',
                'items' => [
                    'Relaksasi, meditasi, dzikir',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring Mandiri',
                'items' => [
                    'Cek tekanan darah harian',
                    'Catat hasil',
                ],
            ],
            [
                'title' => 'Lakukan Dukungan Keluarga',
                'items' => [
                    'Pengawasan obat',
                    'Dukungan gaya hidup sehat',
                ],
            ],
        ],
    ],
];
