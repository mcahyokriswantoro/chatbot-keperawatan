<?php

/**
 * Kuesioner skrining Jantung Koroner — 25 item gejala dan faktor risiko.
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
        ['id' => 'q01', 'no' => 1, 'text' => 'Apakah Anda merasakan nyeri dada seperti ditekan, diremas, atau tertindih sesuatu', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'Apakah nyeri dada Anda menjalar ke lengan kiri, leher, rahang, atau punggung', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'Apakah nyeri dada Anda muncul saat beraktivitas dan membaik saat Anda beristirahat', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'Apakah Anda merasa sesak napas saat beraktivitas, seperti berjalan atau naik tangga', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'Apakah Anda mudah lelah atau cepat capek tanpa sebab yang jelas', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'Apakah Anda berkeringat dingin saat merasakan nyeri di dada', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'Apakah Anda merasa mual atau pusing saat nyeri dada muncul', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'Apakah nyeri dada Anda berlangsung lebih dari 20 menit', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Apakah nyeri dada Anda tidak membaik meskipun sudah beristirahat', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'Apakah Anda merasa sesak napas berat secara mendadak', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'Apakah Anda pernah pingsan atau hampir pingsan secara tiba-tiba', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'Apakah Anda memiliki riwayat tekanan darah tinggi (hipertensi)', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'Apakah Anda memiliki riwayat diabetes melitus atau kencing manis', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah hasil pemeriksaan kolesterol Anda pernah tinggi (dislipidemia)', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'Apakah Anda aktif merokok saat ini', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'Apakah berat badan Anda di atas normal atau Anda mengalami obesitas', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'Apakah Anda jarang berolahraga atau aktivitas fisik Anda rendah', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'Apakah pola makan Anda sering tinggi lemak, gorengan, atau garam', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'Apakah Anda sering merasa stres dalam jangka waktu lama', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'Apakah Anda pernah didiagnosis mengalami penyakit jantung sebelumnya', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'Apakah ada anggota keluarga dekat (ayah, ibu, atau saudara) yang memiliki riwayat penyakit jantung', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'Apakah usia Anda sudah 40 tahun (pria) atau 50 tahun (wanita) ke atas', 'score_ya' => 1],
        ['id' => 'q23', 'no' => 23, 'text' => 'Apakah tekanan darah Anda pernah terukur tinggi, di atas 140/90 mmHg', 'score_ya' => 1],
        ['id' => 'q24', 'no' => 24, 'text' => 'Apakah hasil pemeriksaan gula darah Anda pernah tinggi', 'score_ya' => 1],
        ['id' => 'q25', 'no' => 25, 'text' => 'Apakah hasil pemeriksaan kolesterol LDL Anda pernah tinggi', 'score_ya' => 1],
    ],
];
