<?php

return [

    'bot_name' => 'Chatbot Keperawatan Pintar',

    'welcome' => 'Halo! Saya asisten deteksi kesehatan Anda. Saya akan mengajukan beberapa pertanyaan singkat untuk membantu skrining kondisi kesehatan. Jawaban Anda bersifat rahasia. Siap memulai?',

    'start_options' => [
        ['value' => 'start', 'label' => 'Ya, mulai sekarang'],
        ['value' => 'later', 'label' => 'Nanti saja'],
    ],

    'questions' => [
        [
            'id' => 'age',
            'text' => 'Berapa usia Anda saat ini?',
            'type' => 'choice',
            'options' => [
                ['value' => '<18', 'label' => 'Di bawah 18 tahun'],
                ['value' => '18-30', 'label' => '18 – 30 tahun'],
                ['value' => '31-45', 'label' => '31 – 45 tahun'],
                ['value' => '46-60', 'label' => '46 – 60 tahun'],
                ['value' => '>60', 'label' => 'Di atas 60 tahun'],
            ],
        ],
        [
            'id' => 'symptoms',
            'text' => 'Apakah Anda mengalami keluhan berikut? (bisa pilih lebih dari satu)',
            'type' => 'multi',
            'options' => [
                ['value' => 'demam', 'label' => 'Demam'],
                ['value' => 'batuk', 'label' => 'Batuk / pilek'],
                ['value' => 'sesak', 'label' => 'Sesak napas'],
                ['value' => 'nyeri_dada', 'label' => 'Nyeri dada'],
                ['value' => 'pusing', 'label' => 'Pusing / mual'],
                ['value' => 'tidak_ada', 'label' => 'Tidak ada keluhan'],
            ],
        ],
        [
            'id' => 'duration',
            'text' => 'Berapa lama keluhan tersebut berlangsung?',
            'type' => 'choice',
            'options' => [
                ['value' => '<3', 'label' => 'Kurang dari 3 hari'],
                ['value' => '3-7', 'label' => '3 – 7 hari'],
                ['value' => '>7', 'label' => 'Lebih dari 7 hari'],
                ['value' => 'none', 'label' => 'Tidak berlaku'],
            ],
        ],
        [
            'id' => 'chronic',
            'text' => 'Apakah Anda memiliki riwayat penyakit kronis?',
            'type' => 'choice',
            'options' => [
                ['value' => 'tidak', 'label' => 'Tidak ada'],
                ['value' => 'diabetes', 'label' => 'Diabetes'],
                ['value' => 'hipertensi', 'label' => 'Hipertensi'],
                ['value' => 'jantung', 'label' => 'Penyakit jantung'],
                ['value' => 'lainnya', 'label' => 'Lainnya'],
            ],
        ],
        [
            'id' => 'notes',
            'text' => 'Ada hal lain yang ingin Anda sampaikan? (opsional)',
            'type' => 'text',
            'placeholder' => 'Tulis keluhan tambahan di sini...',
        ],
    ],

    'result' => [
        'title' => 'Skrining Selesai',
        'message' => 'Terima kasih telah menjawab semua pertanyaan. Berikut ringkasan skrining awal Anda. Hasil ini bersifat informatif dan bukan diagnosis medis. Segera konsultasikan ke tenaga kesehatan jika keluhan memberat.',
    ],

];
