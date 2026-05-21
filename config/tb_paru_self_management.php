<?php

/**
 * Panduan self-management TB Paru berdasarkan klasifikasi risiko skrining.
 */
return [
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'sections' => [
            [
                'title' => 'Lakukan Perilaku Hidup Sehat',
                'items' => [
                    'Konsumsi makanan bergizi (tinggi protein: telur, ikan, tempe)',
                    'Istirahat cukup (7–8 jam/hari)',
                    'Olahraga ringan (jalan kaki, senam)',
                ],
            ],
            [
                'title' => 'Lakukan Pencegahan Penularan',
                'items' => [
                    'Hindari kontak dekat dengan penderita TB aktif',
                    'Gunakan masker di lingkungan berisiko',
                    'Terapkan etika batuk',
                ],
            ],
            [
                'title' => 'Jaga Lingkungan Sehat',
                'items' => [
                    'Pastikan rumah memiliki ventilasi baik',
                    'Paparan sinar matahari cukup',
                    'Kurangi kepadatan hunian',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring',
                'items' => [
                    'Lakukan skrining ulang jika muncul gejala',
                    'Edukasi keluarga tentang TB',
                ],
            ],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'sections' => [
            [
                'title' => 'Lakukan Deteksi Dini',
                'items' => [
                    'Segera periksa ke puskesmas/klinik',
                    'Lakukan pemeriksaan dahak jika batuk ≥ 2 minggu',
                ],
            ],
            [
                'title' => 'Lakukan Perilaku Pencegahan',
                'items' => [
                    'Gunakan masker saat batuk',
                    'Tutup mulut saat batuk/bersin',
                    'Hindari berbagi alat makan',
                ],
            ],
            [
                'title' => 'Jaga Nutrisi & Imunitas',
                'items' => [
                    'Tingkatkan asupan protein dan vitamin (A, C, D)',
                    'Minum air cukup',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring Gejala',
                'items' => [
                    'Catat durasi batuk',
                    'Catat penurunan berat badan',
                    'Catat demam atau keringat malam',
                ],
            ],
            [
                'title' => 'Lakukan Edukasi Keluarga',
                'items' => [
                    'Periksa kontak serumah jika ada gejala',
                    'Tingkatkan kesadaran TB',
                ],
            ],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi',
        'sections' => [
            [
                'title' => 'Lakukan Tindakan Segera',
                'items' => [
                    'Periksa ke fasilitas kesehatan (WAJIB)',
                    'Pemeriksaan tes dahak (BTA/TCM)',
                    'Pemeriksaan rontgen dada',
                ],
            ],
            [
                'title' => 'Lakukan Kepatuhan Pengobatan',
                'items' => [
                    'Minum obat TB sesuai program DOTS (6–9 bulan)',
                    'Jangan menghentikan obat tanpa izin dokter',
                    'Minum obat pada waktu yang sama setiap hari',
                ],
            ],
            [
                'title' => 'Lakukan Pencegahan Penularan',
                'items' => [
                    'Gunakan masker setiap hari',
                    'Tidur terpisah sementara',
                    'Jaga ventilasi rumah tetap terbuka',
                    'Etika batuk (tutup dengan tisu/siku)',
                ],
            ],
            [
                'title' => 'Jaga Dukungan Nutrisi',
                'items' => [
                    'Tinggi protein (ikan, ayam, telur)',
                    'Kalori cukup',
                    'Vitamin & mineral',
                ],
            ],
            [
                'title' => 'Lakukan Manajemen Keluarga',
                'items' => [
                    'Skrining semua anggota keluarga',
                    'Edukasi TB pada keluarga',
                    'Hindari kontak dekat dengan anak kecil/lansia',
                ],
            ],
            [
                'title' => 'Lakukan Monitoring Mandiri',
                'items' => [
                    'Catat kepatuhan minum obat',
                    'Catat berat badan',
                    'Catat gejala (batuk, demam, nafsu makan)',
                ],
            ],
        ],
    ],
];
