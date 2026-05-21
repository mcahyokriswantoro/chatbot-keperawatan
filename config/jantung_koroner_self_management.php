<?php

/**
 * Panduan self-management Jantung Koroner berdasarkan klasifikasi risiko skrining.
 */
return [
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'sections' => [
            [
                'title' => 'Jaga Pola Makan Sehat Jantung',
                'items' => [
                    'Kurangi lemak jenuh & gorengan',
                    'Batasi garam (< 5 gram/hari)',
                    'Perbanyak buah, sayur, dan serat',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik',
                'items' => [
                    'Olahraga ≥ 150 menit/minggu',
                    'Jalan cepat, bersepeda, atau senam',
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
                    'Cek tekanan darah, gula darah, kolesterol secara berkala',
                ],
            ],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'sections' => [
            [
                'title' => 'Lakukan Kontrol Faktor Risiko',
                'items' => [
                    'Kendalikan Hipertensi',
                    'Kendalikan Diabetes Mellitus',
                    'Turunkan kolesterol',
                ],
            ],
            [
                'title' => 'Lakukan Diet Jantung Sehat',
                'items' => [
                    'Diet DASH / Mediterania',
                    'Kurangi lemak jenuh & trans',
                    'Batasi gula dan garam',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik Teratur',
                'items' => [
                    'Olahraga ringan–sedang 30 menit/hari',
                ],
            ],
            [
                'title' => 'Kepatuhan Terapi',
                'items' => [
                    'Minum obat sesuai resep (jika sudah ada)',
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
                    'Evaluasi jantung (EKG, lab, dll)',
                ],
            ],
            [
                'title' => 'Lakukan Kepatuhan Terapi',
                'items' => [
                    'Obat antihipertensi',
                    'Obat penurun kolesterol (statin)',
                    'Obat antiplatelet (jika diresepkan)',
                    'Tidak boleh berhenti minum obat tanpa konsultasi dokter',
                ],
            ],
            [
                'title' => 'Lakukan Manajemen Aktivitas',
                'items' => [
                    'Aktivitas ringan terkontrol',
                    'Hindari aktivitas berat mendadak',
                ],
            ],
            [
                'title' => 'Lakukan Manajemen Gejala',
                'items' => [
                    'Istirahat saat nyeri dada',
                    'Gunakan obat sesuai anjuran dokter',
                ],
            ],
            [
                'title' => 'Lakukan Nutrisi Ketat',
                'items' => [
                    'Rendah garam',
                    'Rendah lemak jenuh',
                    'Tinggi serat',
                ],
            ],
            [
                'title' => 'Lakukan Dukungan Keluarga',
                'items' => [
                    'Edukasi keluarga',
                    'Pengawasan obat',
                    'Bantuan aktivitas jika diperlukan',
                ],
            ],
        ],
    ],
];
