<?php

/**
 * Panduan self-management Diabetes Melitus berdasarkan klasifikasi risiko skrining.
 */
return [
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'sections' => [
            [
                'title' => 'Jaga Pola Makan Sehat',
                'items' => [
                    'Batasi gula sederhana (minuman manis, kue)',
                    'Perbanyak serat (sayur, buah, biji-bijian)',
                    'Pilih karbohidrat kompleks',
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
                    'Kelola stres',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring',
                'items' => [
                    'Cek gula darah berkala',
                ],
            ],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'sections' => [
            [
                'title' => 'Lakukan Pengaturan Diet Ketat',
                'items' => [
                    'Kurangi karbohidrat sederhana',
                    'Atur porsi makan (3 utama + 2 snack sehat)',
                    'Hindari minuman manis',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik Teratur',
                'items' => [
                    'Minimal 30 menit/hari',
                    'Kombinasi aerobik & latihan kekuatan',
                ],
            ],
            [
                'title' => 'Lakukan Penurunan Berat Badan',
                'items' => [
                    'Target turun 5–10% dari berat badan',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring Rutin',
                'items' => [
                    'Gula darah puasa',
                    'HbA1c',
                    'Tekanan darah',
                ],
            ],
            [
                'title' => 'Lakukan Edukasi Diri',
                'items' => [
                    'Kenali tanda diabetes',
                    'Konsultasi dengan tenaga kesehatan',
                ],
            ],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi / Diabetes',
        'sections' => [
            [
                'title' => 'Lakukan Tindakan Utama',
                'items' => [
                    'Wajib kontrol rutin ke dokter',
                    'Pemeriksaan gula darah & HbA1c',
                ],
            ],
            [
                'title' => 'Lakukan Kepatuhan Terapi',
                'items' => [
                    'Minum obat antidiabetes / insulin sesuai resep',
                    'Jangan menghentikan obat tanpa konsultasi',
                ],
            ],
            [
                'title' => 'Lakukan Manajemen Diet Diabetes',
                'items' => [
                    'Diet rendah gula',
                    'Karbohidrat terkontrol',
                    'Tinggi serat',
                    'Hindari makanan tinggi indeks glikemik',
                ],
            ],
            [
                'title' => 'Lakukan Aktivitas Fisik',
                'items' => [
                    'Olahraga rutin',
                    'Hindari sedentary lifestyle',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring Mandiri',
                'items' => [
                    'Cek gula darah mandiri',
                    'Catat hasil harian',
                ],
            ],
            [
                'title' => 'Lakukan Perawatan Kaki Diabetes',
                'items' => [
                    'Periksa kaki setiap hari',
                    'Hindari luka',
                    'Gunakan alas kaki',
                ],
            ],
            [
                'title' => 'Lakukan Dukungan Keluarga',
                'items' => [
                    'Pengawasan diet',
                    'Dukungan kepatuhan obat',
                ],
            ],
        ],
    ],
];
