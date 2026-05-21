<?php

/**
 * Kuesioner skrining Stroke — 23 item gejala dan faktor risiko.
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
        ['id' => 'q01', 'no' => 1, 'text' => 'wajah tampak mencong / tidak simetris', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'kelemahan atau mati rasa pada satu sisi tubuh', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'kesulitan berbicara / bicara pelo', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'sulit memahami pembicaraan', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'gangguan penglihatan mendadak (satu atau kedua mata)', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'pusing berat atau kehilangan keseimbangan', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'sakit kepala hebat tiba-tiba tanpa sebab jelas', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'Face: wajah tidak simetris saat tersenyum', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Arm: salah satu lengan lemah/tidak bisa diangkat', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'Speech: bicara tidak jelas', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'Time: gejala terjadi mendadak (dalam hitungan menit/jam)', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'riwayat Hipertensi', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'riwayat Diabetes Mellitus', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'kolesterol tinggi', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'riwayat penyakit jantung (misalnya atrial fibrilasi)', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'merokok aktif', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'konsumsi alkohol berlebihan', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'obesitas / berat badan berlebih', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'kurang aktivitas fisik', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'usia ≥ 40 tahun', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'pernah mengalami stroke sebelumnya', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'pernah mengalami TIA (stroke ringan / sementara)', 'score_ya' => 1],
        ['id' => 'q23', 'no' => 23, 'text' => 'riwayat keluarga dengan stroke', 'score_ya' => 1],
    ],
];
