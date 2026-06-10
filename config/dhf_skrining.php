<?php

/**
 * Kuesioner skrining DHF — 24 gejala dalam 2–7 hari terakhir.
 * Jawaban: Ya → skor 1 | Tidak → 0
 */
return [
    'question_prefix' => '',

    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    'warning_sign_ids' => [
        'q09', 'q10', 'q11', 'q12', 'q13', 'q14', 'q15', 'q16', 'q17', 'q18',
    ],

    'scoring_legend' => 'Risiko Rendah 0–4 · Risiko Sedang 5–8 · Risiko Tinggi ≥9 atau ada warning signs',

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'Dalam 2–7 hari terakhir, apakah Anda mengalami demam tinggi mendadak dengan suhu tubuh 38°C atau lebih', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'Apakah demam yang Anda rasakan berlangsung antara 2 sampai 7 hari', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'Apakah Anda merasakan sakit kepala, terutama di bagian dahi', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'Apakah Anda merasakan nyeri di belakang mata, seperti ditekan dari dalam', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'Apakah otot dan sendi Anda terasa pegal atau sakit, seperti badan dipukul', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'Apakah Anda merasa mual atau pernah muntah dalam beberapa hari terakhir', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'Apakah nafsu makan Anda menurun dan Anda jadi tidak ingin makan seperti biasanya', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'Apakah tubuh Anda terasa lemah, lemas, atau mudah capek', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Apakah muncul bintik-bintik merah kecil di kulit Anda (petechiae)', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q10', 'no' => 10, 'text' => 'Apakah hidung Anda berdarah tanpa sebab yang jelas (mimisan)', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q11', 'no' => 11, 'text' => 'Apakah gusi Anda mudah berdarah, misalnya saat menyikat gigi', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q12', 'no' => 12, 'text' => 'Apakah Anda pernah muntah darah', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q13', 'no' => 13, 'text' => 'Apakah Anda mengalami BAB berwarna hitam seperti aspal (melena)', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah Anda merasakan nyeri perut yang hebat dan terus-menerus', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q15', 'no' => 15, 'text' => 'Apakah Anda muntah terus-menerus dan sulit ditahan', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q16', 'no' => 16, 'text' => 'Apakah ada perdarahan di bagian mulut, seperti gusi atau hidung yang mudah berdarah', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q17', 'no' => 17, 'text' => 'Apakah Anda merasa sangat lemas, gelisah, atau sulit beraktivitas seperti biasanya', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q18', 'no' => 18, 'text' => 'Apakah ada pembesaran hati yang terdeteksi dari pemeriksaan medis', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q19', 'no' => 19, 'text' => 'Jika Anda sudah periksa lab, apakah hasilnya menunjukkan trombosit (platelet) turun', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'Jika Anda sudah periksa lab, apakah hasilnya menunjukkan hematokrit meningkat', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'Apakah Anda tinggal di daerah yang dikenal sering terjadi kasus demam berdarah dengue', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'Apakah ada anggota keluarga atau tetangga di sekitar Anda yang sedang sakit DBD', 'score_ya' => 1],
        ['id' => 'q23', 'no' => 23, 'text' => 'Apakah di rumah atau lingkungan sekitar Anda banyak terlihat nyamuk, terutama nyamuk Aedes', 'score_ya' => 1],
        ['id' => 'q24', 'no' => 24, 'text' => 'Apakah Anda pernah terkena demam berdarah dengue sebelumnya', 'score_ya' => 1],
    ],
];
