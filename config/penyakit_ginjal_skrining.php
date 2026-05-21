<?php

/**
 * Kuesioner skrining Penyakit Ginjal — 26 item (beberapa minggu/bulan terakhir).
 * Jawaban: Ya → skor 1 | Tidak → 0
 *
 * Klasifikasi:
 * - Risiko Rendah: 0–5 poin
 * - Risiko Sedang: 6–10 poin
 * - Risiko Tinggi: ≥ 11 poin
 */
return [
    'question_prefix' => 'Apakah dalam beberapa minggu/bulan terakhir mengalami',

    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    'scoring_legend' => 'Risiko Rendah 0–5 · Risiko Sedang 6–10 · Risiko Tinggi ≥11',

    'tinggi_min' => 11,
    'sedang_min' => 6,

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'sering merasa lelah tanpa sebab jelas', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'nafsu makan menurun', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'mual atau muntah', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'gatal-gatal pada kulit', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'bengkak pada kaki, tangan, atau wajah', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'sesak napas', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'nyeri pinggang / area ginjal', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'kram otot (terutama malam hari)', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'frekuensi kencing meningkat (terutama malam hari/nocturia)', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'volume urin berkurang', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'urin berbusa (indikasi proteinuria)', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'urin berwarna gelap / berdarah', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'nyeri saat berkemih', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'riwayat Diabetes Mellitus', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'riwayat Hipertensi', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'riwayat penyakit jantung', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'riwayat keluarga dengan penyakit ginjal', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'usia ≥ 40 tahun', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'konsumsi obat jangka panjang (obat nyeri, herbal, dll)', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'riwayat infeksi saluran kemih berulang', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'pernah mengalami batu ginjal', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'riwayat dehidrasi berat berulang', 'score_ya' => 1],
        ['id' => 'q23', 'no' => 23, 'text' => 'tekanan darah tinggi (>140/90 mmHg)', 'score_ya' => 1],
        ['id' => 'q24', 'no' => 24, 'text' => 'gula darah tinggi', 'score_ya' => 1],
        ['id' => 'q25', 'no' => 25, 'text' => 'kreatinin meningkat', 'score_ya' => 1],
        ['id' => 'q26', 'no' => 26, 'text' => 'protein urin positif', 'score_ya' => 1],
    ],
];
