<?php

return [

    'skrining_awal' => [
        'label' => 'Skrining Awal',
        'icon' => '🔍',
        'color' => 'blue',
        'scoring' => false,
        'advanced' => false,
        'description' => 'Skrining awal 20 pertanyaan untuk menentukan skrining lanjut yang direkomendasikan',
        'welcome' => 'Halo! Saya akan menemani skrining awal melalui 20 pertanyaan singkat tentang gejala dan faktor risiko. Jawab ya atau tidak sesuai kondisi Anda. Di akhir, saya akan rekomendasikan skrining lanjut yang sesuai. Siap mulai?',
        'questions' => [],
    ],

    'tb_paru' => [
        'advanced' => true,
        'label' => 'TB Paru',
        'icon' => '🫁',
        'color' => 'sky',
        'scoring' => true,
        'description' => 'Periksa gejala dan faktor risiko tuberkulosis paru — 23 pertanyaan singkat',
        'welcome' => 'Halo! Saya akan menemani Anda mengecek risiko TB Paru melalui 23 pertanyaan sederhana. Jawab dengan jujur ya atau tidak sesuai kondisi Anda. Di akhir, saya akan rangkum skor dan saran langkah selanjutnya. Siap mulai?',
        'questions' => [], // diisi dari TbParuScoringService via DetectionController
    ],

    'dhf' => [
        'label' => 'DHF',
        'icon' => '🦟',
        'color' => 'amber',
        'scoring' => true,
        'description' => 'Cek gejala demam berdarah dengue dalam 7 hari terakhir — 24 pertanyaan',
        'welcome' => 'Halo! Mari kita cek bersama apakah gejala yang Anda rasakan mengarah ke demam berdarah dengue. Saya akan bertanya seputar kondisi Anda dalam 2–7 hari terakhir. Jawab ya atau tidak dengan santai, lalu saya berikan ringkasan risikonya. Siap mulai?',
        'questions' => [], // diisi dari DhfScoringService via DetectionController
    ],

    'ppok' => [
        'label' => 'PPOK',
        'icon' => '💨',
        'color' => 'teal',
        'scoring' => true,
        'description' => 'Evaluasi gejala paru kronis dan faktor risiko — 19 pertanyaan',
        'welcome' => 'Halo! Saya akan membantu menilai kesehatan paru Anda melalui 19 pertanyaan tentang gejala dan kebiasaan sehari-hari dalam beberapa bulan terakhir. Tidak perlu buru-buru — jawab sesuai yang Anda rasakan. Siap mulai?',
        'questions' => [], // diisi dari PpokScoringService via DetectionController
    ],

    'penyakit_ginjal' => [
        'label' => 'Penyakit Ginjal',
        'icon' => '🫘',
        'color' => 'violet',
        'scoring' => true,
        'description' => 'Tinjau gejala dan faktor risiko gangguan ginjal — 26 pertanyaan',
        'welcome' => 'Halo! Mari kita periksa kondisi ginjal Anda lewat 26 pertanyaan tentang gejala dan riwayat kesehatan. Jawab ya atau tidak sesuai pengalaman Anda beberapa minggu atau bulan terakhir. Saya akan bantu rangkum hasilnya di akhir. Siap mulai?',
        'questions' => [], // diisi dari PenyakitGinjalScoringService via DetectionController
    ],

    'stroke' => [
        'label' => 'Stroke',
        'icon' => '🧠',
        'color' => 'rose',
        'scoring' => true,
        'description' => 'Kenali tanda stroke dan faktor risikonya — 23 pertanyaan',
        'welcome' => 'Halo! Saya akan menanyakan gejala dan faktor risiko stroke melalui 23 pertanyaan. Jika saat ini Anda mengalami gejala mendadak seperti wajah mencong, lengan lemah, atau bicara pelo, segera ke IGD — jangan tunggu skrining selesai. Kalau tidak ada gejala darurat, mari kita mulai ya?',
        'questions' => [], // diisi dari StrokeScoringService via DetectionController
    ],

    'jantung_koroner' => [
        'label' => 'Jantung Koroner',
        'icon' => '❤️',
        'color' => 'red',
        'scoring' => true,
        'description' => 'Periksa gejala dan faktor risiko jantung koroner — 25 pertanyaan',
        'welcome' => 'Halo! Mari kita cek kesehatan jantung Anda lewat 25 pertanyaan tentang gejala dan gaya hidup. Bila saat ini nyeri dada Anda sangat hebat dan tidak membaik, segera cari pertolongan medis. Kalau tidak, jawab pertanyaan berikut sesuai kondisi Anda. Siap mulai?',
        'questions' => [], // diisi dari JantungKoronerScoringService via DetectionController
    ],

    'diabetes_melitus' => [
        'label' => 'Diabetes Melitus',
        'icon' => '🩸',
        'color' => 'orange',
        'scoring' => true,
        'description' => 'Cek gejala dan faktor risiko diabetes — 23 pertanyaan',
        'welcome' => 'Halo! Saya akan menemani Anda menilai risiko diabetes melitus melalui 23 pertanyaan tentang gejala dan kebiasaan sehari-hari. Jawab dengan jujur ya atau tidak — tidak ada jawaban benar atau salah. Siap mulai?',
        'questions' => [], // diisi dari DiabetesMelitusScoringService via DetectionController
    ],

    'hipertensi' => [
        'label' => 'Hipertensi',
        'icon' => '📈',
        'color' => 'indigo',
        'scoring' => true,
        'description' => 'Tinjau gejala dan faktor risiko tekanan darah tinggi — 20 pertanyaan',
        'welcome' => 'Halo! Mari kita periksa bersama apakah ada tanda atau faktor risiko hipertensi pada diri Anda. Saya punya 20 pertanyaan singkat — jawab sesuai yang Anda rasakan, lalu saya rangkum hasilnya. Siap mulai?',
        'questions' => [], // diisi dari HipertensiScoringService via DetectionController
    ],

    'rheumatoid_arthritis' => [
        'label' => 'Rheumatoid Arthritis (RA)',
        'icon' => '🦴',
        'color' => 'purple',
        'scoring' => true,
        'description' => 'Skrining gejala dan faktor risiko arthritis reumatoid — 16 pertanyaan',
        'welcome' => 'Halo! Saya akan menemani Anda menilai risiko rheumatoid arthritis (RA) melalui 16 pertanyaan tentang gejala sendi dan faktor risiko. Jawab dengan jujur ya atau tidak — tidak ada jawaban benar atau salah. Siap mulai?',
        'questions' => [], // diisi dari RheumatoidArthritisScoringService via DetectionController
    ],

];
