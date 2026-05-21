<?php

/**
 * Kuesioner skrining PPOK — 19 item gejala/faktor risiko (≥ 3 bulan terakhir).
 * Jawaban: Ya → skor 1 | Tidak → 0
 *
 * Klasifikasi:
 * - Risiko Rendah: 0–4 poin
 * - Risiko Sedang: 5–8 poin
 * - Risiko Tinggi: ≥ 9 poin
 */
return [
    'question_prefix' => 'Apakah dalam waktu ≥ 3 bulan terakhir Anda mengalami',

    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    'scoring_legend' => 'Risiko Rendah 0–4 · Risiko Sedang 5–8 · Risiko Tinggi ≥9',

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'batuk kronis (hampir setiap hari)', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'batuk berdahak (produksi sputum)', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'sesak napas saat aktivitas ringan', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'napas berbunyi mengi (wheezing)', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'mudah lelah saat beraktivitas', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'merokok aktif (≥ 10 batang/hari atau ≥ 10 tahun)', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'pernah merokok (mantan perokok)', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'terpapar asap rokok (perokok pasif)', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'terpapar asap dapur (kayu, arang, biomassa)', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'paparan debu/kimia di tempat kerja', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'tinggal di lingkungan dengan polusi udara tinggi', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'riwayat ISPA berulang', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'riwayat asma', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'pernah didiagnosis penyakit paru sebelumnya', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'usia ≥ 40 tahun', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'napas cepat', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'penggunaan otot bantu napas', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'dada terasa berat / sempit', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'penurunan berat badan tanpa sebab jelas', 'score_ya' => 1],
    ],
];
