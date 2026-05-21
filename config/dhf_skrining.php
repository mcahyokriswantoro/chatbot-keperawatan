<?php

/**
 * Kuesioner skrining DHF — 24 gejala dalam 2–7 hari terakhir.
 * Jawaban: Ya → skor 1 | Tidak → 0
 *
 * Klasifikasi:
 * - Risiko Rendah: 0–4 poin
 * - Risiko Sedang: 5–8 poin
 * - Risiko Tinggi: ≥ 9 poin ATAU ada warning signs
 */
return [
    'question_prefix' => 'Apakah dalam 2–7 hari terakhir Anda mengalami',

    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    /** Item dengan jawaban Ya → risiko tinggi (warning signs) */
    'warning_sign_ids' => [
        'q09', 'q10', 'q11', 'q12', 'q13', 'q14', 'q15', 'q16', 'q17', 'q18',
    ],

    'scoring_legend' => 'Risiko Rendah 0–4 · Risiko Sedang 5–8 · Risiko Tinggi ≥9 atau ada warning signs',

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'demam tinggi mendadak (≥ 38°C)', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'demam berlangsung 2–7 hari', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'nyeri kepala (terutama di dahi)', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'nyeri belakang mata (retro-orbital pain)', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'nyeri otot dan sendi (pegal-pegal berat)', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'mual atau muntah', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'nafsu makan menurun', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'badan terasa lemah / lelah', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'muncul bintik merah di kulit (petechiae)', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q10', 'no' => 10, 'text' => 'mimisan (epistaksis)', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q11', 'no' => 11, 'text' => 'gusi berdarah', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q12', 'no' => 12, 'text' => 'muntah darah', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q13', 'no' => 13, 'text' => 'BAB hitam (melena)', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q14', 'no' => 14, 'text' => 'nyeri perut hebat / terus-menerus', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q15', 'no' => 15, 'text' => 'muntah terus-menerus', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q16', 'no' => 16, 'text' => 'perdarahan mukosa (gusi/hidung)', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q17', 'no' => 17, 'text' => 'lemas berat / gelisah', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q18', 'no' => 18, 'text' => 'pembesaran hati', 'score_ya' => 1, 'warning_sign' => true],
        ['id' => 'q19', 'no' => 19, 'text' => 'penurunan trombosit (jika ada hasil lab)', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'kenaikan hematokrit (jika ada hasil lab)', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'tinggal di daerah endemis dengue', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'ada anggota keluarga/tetangga sakit DBD', 'score_ya' => 1],
        ['id' => 'q23', 'no' => 23, 'text' => 'banyak nyamuk di rumah/lingkungan', 'score_ya' => 1],
        ['id' => 'q24', 'no' => 24, 'text' => 'pernah terkena DBD sebelumnya', 'score_ya' => 1],
    ],
];
