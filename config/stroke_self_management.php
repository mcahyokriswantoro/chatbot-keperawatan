<?php

/**
 * Panduan self-management Stroke berdasarkan klasifikasi risiko skrining.
 */
return [
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'sections' => [
            [
                'title' => 'Lakukan Gaya Hidup Sehat',
                'items' => [
                    'Pola makan sehat (rendah garam, rendah lemak jenuh)',
                    'Perbanyak buah & sayur',
                    'Batasi gula dan makanan olahan',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik',
                'items' => [
                    'Olahraga ≥ 150 menit/minggu (jalan cepat, bersepeda)',
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
                    'Cek tekanan darah berkala',
                    'Skrining ulang secara rutin',
                ],
            ],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'sections' => [
            [
                'title' => 'Lakukan Kontrol Faktor Risiko Utama',
                'items' => [
                    'Kendalikan Hipertensi',
                    'Kendalikan Diabetes Mellitus',
                    'Kelola kolesterol',
                ],
            ],
            [
                'title' => 'Lakukan Diet Jantung Sehat',
                'items' => [
                    'Diet DASH / Mediterania',
                    'Kurangi garam (< 5 gram/hari)',
                    'Kurangi lemak jenuh & gorengan',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik Teratur',
                'items' => [
                    'Olahraga ringan–sedang (30 menit/hari)',
                ],
            ],
            [
                'title' => 'Lakukan Kepatuhan Pengobatan',
                'items' => [
                    'Minum obat sesuai resep (jika sudah ada terapi)',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring',
                'items' => [
                    'Tekanan darah',
                    'Gula darah',
                    'Kolesterol',
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
                    'Evaluasi menyeluruh (jantung, pembuluh darah, otak)',
                ],
            ],
            [
                'title' => 'Lakukan Kepatuhan Terapi',
                'items' => [
                    'Obat antihipertensi',
                    'Obat antiplatelet / pengencer darah',
                    'Obat diabetes / kolesterol',
                    'Tidak boleh berhenti minum obat tanpa konsultasi dokter',
                ],
            ],
            [
                'title' => 'Lakukan Rehabilitasi & Latihan',
                'items' => [
                    'Latihan fisik sesuai kemampuan',
                    'Latihan bicara (jika terganggu)',
                    'Latihan keseimbangan',
                ],
            ],
            [
                'title' => 'Lakukan Manajemen Aktivitas',
                'items' => [
                    'Istirahat cukup',
                    'Hindari kelelahan berlebihan',
                ],
            ],
            [
                'title' => 'Lakukan Pemberian Nutrisi',
                'items' => [
                    'Diet rendah garam',
                    'Tinggi serat',
                    'Batasi lemak jenuh',
                ],
            ],
            [
                'title' => 'Lakukan Dukungan Keluarga',
                'items' => [
                    'Pendampingan aktivitas',
                    'Pengawasan obat',
                    'Edukasi tanda bahaya stroke',
                ],
            ],
        ],
    ],
];
