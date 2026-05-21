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
        'scoring' => true,
        'description' => 'Skrining gangguan fungsi ginjal (26 pertanyaan, skor ya/tidak)',
        'welcome' => 'Halo! Skrining Penyakit Ginjal terdiri dari 26 pertanyaan gejala dan faktor risiko dalam beberapa minggu/bulan terakhir. Untuk setiap pertanyaan, pilih Ya atau Tidak. Jika Ya, Anda mendapat skor 1. Di akhir akan ditampilkan jumlah skor dan klasifikasi risiko. Siap memulai?',
        'questions' => [], // diisi dari PenyakitGinjalScoringService via DetectionController
    ],

    'stroke' => [
        'label' => 'Stroke',
        'icon' => '🧠',
        'color' => 'rose',
        'scoring' => true,
        'description' => 'Skrining gejala stroke (23 pertanyaan, skor ya/tidak)',
        'welcome' => 'Halo! Skrining Stroke terdiri dari 23 pertanyaan gejala dan faktor risiko. Untuk setiap pertanyaan, pilih Ya atau Tidak. Jika Ya, Anda mendapat skor 1. Di akhir akan ditampilkan jumlah skor dan klasifikasi risiko. Jika ada gejala mendadak, segera ke IGD. Siap memulai?',
        'questions' => [], // diisi dari StrokeScoringService via DetectionController
    ],

    'jantung_koroner' => [
        'label' => 'Jantung Koroner',
        'icon' => '❤️',
        'color' => 'red',
        'scoring' => true,
        'description' => 'Skrining penyakit jantung koroner (25 pertanyaan, skor ya/tidak)',
        'welcome' => 'Halo! Skrining Jantung Koroner terdiri dari 25 pertanyaan gejala dan faktor risiko. Untuk setiap pertanyaan, pilih Ya atau Tidak. Jika Ya, Anda mendapat skor 1. Di akhir akan ditampilkan jumlah skor dan klasifikasi risiko. Nyeri dada hebat memerlukan pertolongan segera. Siap memulai?',
        'questions' => [], // diisi dari JantungKoronerScoringService via DetectionController
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
