<?php

/**
 * Kuesioner skrining Stroke — 23 item gejala dan faktor risiko.
 * Jawaban: Ya → skor 1 | Tidak → 0
 */
return [
    'question_prefix' => '',

    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    'scoring_legend' => 'Risiko Rendah 0–5 · Risiko Sedang 6–10 · Risiko Tinggi ≥11',

    'tinggi_min' => 11,
    'sedang_min' => 6,

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'Apakah wajah Anda terlihat mencong atau tidak simetris, misalnya saat tersenyum satu sisi wajah turun', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'Apakah Anda merasakan kelemahan atau mati rasa pada satu sisi tubuh, seperti tangan atau kaki', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'Apakah Anda mengalami kesulitan berbicara atau bicara terdengar pelo dan tidak jelas', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'Apakah Anda kesulitan memahami apa yang dikatakan orang lain', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'Apakah penglihatan Anda tiba-tiba kabur, gelap, atau ganda pada satu atau kedua mata', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'Apakah Anda merasa pusing berat, jatuh, atau kehilangan keseimbangan secara mendadak', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'Apakah Anda mengalami sakit kepala yang sangat hebat secara tiba-tiba tanpa sebab yang jelas', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'Saat diminta tersenyum, apakah satu sisi wajah Anda terlihat tidak simetris (tanda FAST: Face)', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Saat mengangkat kedua lengan, apakah salah satu lengan terasa lemah atau tidak bisa diangkat (tanda FAST: Arm)', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'Apakah bicara Anda tiba-tiba tidak jelas atau sulit dipahami (tanda FAST: Speech)', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'Apakah gejala-gejala di atas muncul secara mendadak dalam hitungan menit atau jam (tanda FAST: Time)', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'Apakah Anda memiliki riwayat tekanan darah tinggi (hipertensi)', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'Apakah Anda memiliki riwayat diabetes melitus atau kencing manis', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah hasil pemeriksaan kolesterol Anda pernah tinggi', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'Apakah Anda pernah didiagnosis mengalami penyakit jantung, seperti gangguan irama jantung (atrial fibrilasi)', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'Apakah Anda aktif merokok saat ini', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'Apakah Anda mengonsumsi alkohol dalam jumlah banyak atau secara rutin', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'Apakah berat badan Anda di atas normal atau Anda mengalami obesitas', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'Apakah Anda jarang berolahraga atau aktivitas fisik Anda rendah', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'Apakah usia Anda sudah 40 tahun atau lebih', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'Apakah Anda pernah mengalami stroke sebelumnya', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'Apakah Anda pernah mengalami gejala stroke ringan yang hilang sendiri (TIA atau serangan iskemik sementara)', 'score_ya' => 1],
        ['id' => 'q23', 'no' => 23, 'text' => 'Apakah ada anggota keluarga dekat Anda yang pernah mengalami stroke', 'score_ya' => 1],
    ],
];
