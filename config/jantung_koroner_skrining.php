<?php

/**
 * Kuesioner skrining Jantung Koroner — 25 item gejala dan faktor risiko.
 * Jawaban: Ya → skor 1 | Tidak → 0
 *
 * Klasifikasi:
 * - Risiko Rendah: 0–5 poin
 * - Risiko Sedang: 6–10 poin
 * - Risiko Tinggi: ≥ 11 poin
 */
return [
    'question_prefix' => 'Apakah Anda mengalami',

    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    'scoring_legend' => 'Risiko Rendah 0–5 · Risiko Sedang 6–10 · Risiko Tinggi ≥11',

    'tinggi_min' => 11,
    'sedang_min' => 6,

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'nyeri dada seperti ditekan/tertindih', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'nyeri menjalar ke lengan kiri/leher/rahang/punggung', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'nyeri dada saat aktivitas dan membaik saat istirahat', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'sesak napas saat aktivitas', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'mudah lelah tanpa sebab jelas', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'keringat dingin saat nyeri dada', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'mual atau pusing saat nyeri dada', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'nyeri dada hebat > 20 menit', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'nyeri dada tidak membaik dengan istirahat', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'sesak napas berat mendadak', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'pingsan atau hampir pingsan', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'riwayat Hipertensi', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'riwayat Diabetes Mellitus', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'kolesterol tinggi (dislipidemia)', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'merokok aktif', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'obesitas / berat badan berlebih', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'kurang aktivitas fisik', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'pola makan tinggi lemak/garam', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'stres kronis', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'riwayat penyakit jantung sebelumnya', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'riwayat keluarga penyakit jantung (ayah/ibu/saudara)', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'usia ≥ 40 tahun (pria) / ≥ 50 tahun (wanita)', 'score_ya' => 1],
        ['id' => 'q23', 'no' => 23, 'text' => 'tekanan darah tinggi (>140/90 mmHg)', 'score_ya' => 1],
        ['id' => 'q24', 'no' => 24, 'text' => 'gula darah tinggi', 'score_ya' => 1],
        ['id' => 'q25', 'no' => 25, 'text' => 'kolesterol LDL tinggi', 'score_ya' => 1],
    ],
];
