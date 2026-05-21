<?php

return [

    'tb_paru' => [
        'label' => 'TB Paru',
        'icon' => '🫁',
        'color' => 'sky',
        'scoring' => true,
        'description' => 'Skrining risiko tuberkulosis paru (23 pertanyaan baku)',
        'welcome' => 'Halo! Skrining TB Paru terdiri dari 23 pertanyaan baku. Untuk setiap pertanyaan, pilih Ya atau Tidak. Jika Ya, Anda mendapat skor sesuai tabel; jika Tidak, skor 0. Di akhir akan ditampilkan jumlah nilai akhir. Siap memulai?',
        'questions' => [], // diisi dari TbParuScoringService via DetectionController
    ],

    'dhf' => [
        'label' => 'DHF',
        'icon' => '🦟',
        'color' => 'amber',
        'scoring' => true,
        'description' => 'Skrining demam berdarah dengue (24 gejala, skor ya/tidak)',
        'welcome' => 'Halo! Skrining DHF terdiri dari 24 pertanyaan gejala dalam 2–7 hari terakhir. Untuk setiap gejala, pilih Ya atau Tidak. Jika Ya, Anda mendapat skor 1. Di akhir akan ditampilkan jumlah skor dan klasifikasi risiko. Siap memulai?',
        'questions' => [], // diisi dari DhfScoringService via DetectionController
    ],

    'ppok' => [
        'label' => 'PPOK',
        'icon' => '💨',
        'color' => 'teal',
        'scoring' => true,
        'description' => 'Skrining penyakit paru obstruktif kronik (19 pertanyaan, skor ya/tidak)',
        'welcome' => 'Halo! Skrining PPOK terdiri dari 19 pertanyaan gejala dan faktor risiko dalam ≥ 3 bulan terakhir. Untuk setiap pertanyaan, pilih Ya atau Tidak. Jika Ya, Anda mendapat skor 1. Di akhir akan ditampilkan jumlah skor dan klasifikasi risiko. Siap memulai?',
        'questions' => [], // diisi dari PpokScoringService via DetectionController
    ],

    'penyakit_ginjal' => [
        'label' => 'Penyakit Ginjal',
        'icon' => '🫘',
        'color' => 'violet',
        'description' => 'Skrining gangguan fungsi ginjal',
        'welcome' => 'Halo! Mari lakukan skrining awal Penyakit Ginjal. Beberapa pertanyaan singkat akan membantu menilai risiko. Siap memulai?',
        'questions' => [
            [
                'id' => 'gejala_ginjal',
                'text' => 'Gejala yang Anda rasakan? (bisa pilih lebih dari satu)',
                'type' => 'multi',
                'options' => [
                    ['value' => 'bengkak', 'label' => 'Bengkak kaki/mata'],
                    ['value' => 'urine', 'label' => 'Perubahan buang air kecil'],
                    ['value' => 'lelah', 'label' => 'Mudah lelah'],
                    ['value' => 'gatal', 'label' => 'Gatal-gatal'],
                    ['value' => 'mual', 'label' => 'Mual/muntah'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak ada'],
                ],
            ],
            [
                'id' => 'faktor_risiko',
                'text' => 'Apakah Anda memiliki faktor risiko berikut?',
                'type' => 'multi',
                'options' => [
                    ['value' => 'diabetes', 'label' => 'Diabetes'],
                    ['value' => 'hipertensi', 'label' => 'Hipertensi'],
                    ['value' => 'riwayat_keluarga', 'label' => 'Riwayat penyakit ginjal keluarga'],
                    ['value' => 'obat_nefro', 'label' => 'Penggunaan obat jangka panjang'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak ada'],
                ],
            ],
        ],
    ],

    'stroke' => [
        'label' => 'Stroke',
        'icon' => '🧠',
        'color' => 'rose',
        'description' => 'Skrining gejala stroke',
        'welcome' => 'Halo! Saya akan membantu skrining awal Stroke menggunakan gejala umum (FAST). Jika ada tanda darurat, segera ke IGD. Siap memulai?',
        'questions' => [
            [
                'id' => 'fast',
                'text' => 'Gejala yang Anda alami saat ini? (bisa pilih lebih dari satu)',
                'type' => 'multi',
                'options' => [
                    ['value' => 'wajah', 'label' => 'Wajah mencong / lemas separuh'],
                    ['value' => 'lengan', 'label' => 'Lengan lemah/tidak bisa diangkat'],
                    ['value' => 'bicara', 'label' => 'Bicara pelo/tidak jelas'],
                    ['value' => 'sakit_kepala', 'label' => 'Sakit kepala hebat mendadak'],
                    ['value' => 'pandangan', 'label' => 'Pandangan kabur/ganda'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak ada'],
                ],
            ],
            [
                'id' => 'waktu_mulai',
                'text' => 'Kapan gejala mulai dirasakan?',
                'type' => 'choice',
                'options' => [
                    ['value' => '<24jam', 'label' => 'Kurang dari 24 jam'],
                    ['value' => '1-7hari', 'label' => '1 – 7 hari'],
                    ['value' => '>7hari', 'label' => 'Lebih dari 7 hari'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak berlaku'],
                ],
            ],
        ],
    ],

    'jantung_koroner' => [
        'label' => 'Jantung Koroner',
        'icon' => '❤️',
        'color' => 'red',
        'description' => 'Skrining penyakit jantung koroner',
        'welcome' => 'Halo! Mari lakukan skrining awal Jantung Koroner. Nyeri dada hebat memerlukan pertolongan segera. Siap memulai?',
        'questions' => [
            [
                'id' => 'nyeri_dada',
                'text' => 'Apakah Anda merasakan nyeri atau tekanan di dada?',
                'type' => 'choice',
                'options' => [
                    ['value' => 'ya_berat', 'label' => 'Ya, hebat'],
                    ['value' => 'ya_ringan', 'label' => 'Ya, ringan'],
                    ['value' => 'tidak', 'label' => 'Tidak'],
                ],
            ],
            [
                'id' => 'gejala_jantung',
                'text' => 'Gejala pendamping? (bisa pilih lebih dari satu)',
                'type' => 'multi',
                'options' => [
                    ['value' => 'sesak', 'label' => 'Sesak napas'],
                    ['value' => 'keringat', 'label' => 'Keringat dingin'],
                    ['value' => 'mual', 'label' => 'Mual/muntah'],
                    ['value' => 'kebas', 'label' => 'Kebas di lengan/rahang'],
                    ['value' => 'pusing', 'label' => 'Pusing'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak ada'],
                ],
            ],
            [
                'id' => 'faktor_risiko',
                'text' => 'Faktor risiko yang Anda miliki?',
                'type' => 'multi',
                'options' => [
                    ['value' => 'hipertensi', 'label' => 'Hipertensi'],
                    ['value' => 'diabetes', 'label' => 'Diabetes'],
                    ['value' => 'rokok', 'label' => 'Merokok'],
                    ['value' => 'keluarga', 'label' => 'Riwayat penyakit jantung keluarga'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak ada'],
                ],
            ],
        ],
    ],

    'diabetes_melitus' => [
        'label' => 'Diabetes Melitus',
        'icon' => '🩸',
        'color' => 'orange',
        'description' => 'Skrining diabetes melitus',
        'welcome' => 'Halo! Saya akan membantu skrining awal Diabetes Melitus. Mari periksa gejala dan faktor risiko Anda. Siap memulai?',
        'questions' => [
            [
                'id' => 'gejala_dm',
                'text' => 'Gejala yang Anda alami? (bisa pilih lebih dari satu)',
                'type' => 'multi',
                'options' => [
                    ['value' => 'haus', 'label' => 'Haus berlebihan'],
                    ['value' => 'banyak_urine', 'label' => 'Sering buang air kecil'],
                    ['value' => 'bb_turun', 'label' => 'Berat badan turun tanpa sebab'],
                    ['value' => 'luka_lambat', 'label' => 'Luka sulit sembuh'],
                    ['value' => 'mudah_lelah', 'label' => 'Mudah lelah'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak ada'],
                ],
            ],
            [
                'id' => 'gula_terukur',
                'text' => 'Apakah pernah diperiksa gula darah puasa ≥ 126 mg/dL atau HbA1c tinggi?',
                'type' => 'choice',
                'options' => [
                    ['value' => 'ya', 'label' => 'Ya'],
                    ['value' => 'tidak', 'label' => 'Tidak / belum pernah'],
                    ['value' => 'tidak_tahu', 'label' => 'Tidak tahu'],
                ],
            ],
            [
                'id' => 'faktor_risiko',
                'text' => 'Faktor risiko yang Anda miliki?',
                'type' => 'multi',
                'options' => [
                    ['value' => 'keluarga', 'label' => 'Riwayat diabetes keluarga'],
                    ['value' => 'obesitas', 'label' => 'Obesitas'],
                    ['value' => 'kurang_aktif', 'label' => 'Kurang aktivitas fisik'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak ada'],
                ],
            ],
        ],
    ],

    'hipertensi' => [
        'label' => 'Hipertensi',
        'icon' => '📈',
        'color' => 'indigo',
        'description' => 'Skrining tekanan darah tinggi',
        'welcome' => 'Halo! Mari lakukan skrining awal Hipertensi (tekanan darah tinggi). Siap menjawab beberapa pertanyaan?',
        'questions' => [
            [
                'id' => 'tekanan_terukur',
                'text' => 'Apakah pernah diukur tekanan darah ≥ 140/90 mmHg?',
                'type' => 'choice',
                'options' => [
                    ['value' => 'ya', 'label' => 'Ya'],
                    ['value' => 'tidak', 'label' => 'Tidak / belum pernah'],
                    ['value' => 'tidak_tahu', 'label' => 'Tidak tahu'],
                ],
            ],
            [
                'id' => 'gejala_ht',
                'text' => 'Gejala yang Anda rasakan? (bisa pilih lebih dari satu)',
                'type' => 'multi',
                'options' => [
                    ['value' => 'sakit_kepala', 'label' => 'Sakit kepala'],
                    ['value' => 'pusing', 'label' => 'Pusing'],
                    ['value' => 'pandangan', 'label' => 'Pandangan kabur'],
                    ['value' => 'sesak', 'label' => 'Sesak napas'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak ada gejala'],
                ],
            ],
            [
                'id' => 'faktor_risiko',
                'text' => 'Faktor risiko yang Anda miliki?',
                'type' => 'multi',
                'options' => [
                    ['value' => 'keluarga', 'label' => 'Riwayat hipertensi keluarga'],
                    ['value' => 'garam', 'label' => 'Konsumsi garam tinggi'],
                    ['value' => 'obesitas', 'label' => 'Obesitas'],
                    ['value' => 'stres', 'label' => 'Stres berlebihan'],
                    ['value' => 'tidak_ada', 'label' => 'Tidak ada'],
                ],
            ],
        ],
    ],

];
