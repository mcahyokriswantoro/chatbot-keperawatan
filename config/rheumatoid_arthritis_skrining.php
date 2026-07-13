<?php

/**
 * Instrumen skrining lanjut Rheumatoid Arthritis (RA) — 16 item gejala dan faktor risiko.
 * Jawaban: Ya → skor 1 | Tidak → 0
 */
return [
    'question_prefix' => '',

    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    'scoring_legend' => 'Risiko Rendah (0–4 poin) · Risiko Sedang (5–9 poin) · Risiko Tinggi (≥ 10 poin)',

    'tinggi_min' => 10,
    'sedang_min' => 5,

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'Apakah Anda mengalami nyeri pada lebih dari satu sendi?', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'Apakah nyeri terjadi pada kedua sisi tubuh (misalnya kedua tangan atau kedua lutut)?', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'Apakah sendi terasa kaku saat bangun pagi selama lebih dari 30 menit?', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'Apakah keluhan berlangsung lebih dari 6 minggu?', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'Apakah terdapat pembengkakan pada sendi?', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'Apakah sendi terasa hangat atau kemerahan?', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'Apakah Anda kesulitan menggenggam atau memegang benda?', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'Apakah Anda kesulitan membuka tutup botol atau memutar gagang pintu?', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Apakah aktivitas sehari-hari terganggu akibat nyeri sendi?', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'Apakah Anda sering merasa lelah atau cepat lelah?', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'Apakah nafsu makan menurun dalam beberapa minggu terakhir?', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'Apakah berat badan turun tanpa sebab yang jelas?', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah ada anggota keluarga yang menderita rheumatoid arthritis atau penyakit autoimun?', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'Apakah Anda seorang perokok atau pernah merokok?', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'Apakah usia Anda lebih dari 40 tahun?', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'Apakah Anda perempuan?', 'score_ya' => 1],
    ],
];
