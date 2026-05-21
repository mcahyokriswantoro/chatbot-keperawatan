<?php

/**
 * Kuesioner skrining Hipertensi — 20 item gejala dan faktor risiko.
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
        ['id' => 'q01', 'no' => 1, 'text' => 'sakit kepala (terutama bagian belakang)', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'pusing atau rasa berputar', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'mudah lelah', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'penglihatan kabur', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'mimisan', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'jantung berdebar', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'usia ≥ 40 tahun', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'riwayat keluarga dengan hipertensi', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'konsumsi garam tinggi', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'kurang aktivitas fisik', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'obesitas / berat badan berlebih', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'merokok', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'konsumsi alkohol', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'stres berkepanjangan', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'riwayat Diabetes Mellitus', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'riwayat penyakit ginjal', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'kolesterol tinggi', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'riwayat penyakit jantung', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'tekanan darah ≥ 140/90 mmHg', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'pernah didiagnosis hipertensi oleh tenaga kesehatan', 'score_ya' => 1],
    ],
];
