<?php

/**
 * Kuesioner skrining Hipertensi — 20 item gejala dan faktor risiko.
 * Jawaban: Ya → skor 1 | Tidak → 0
 */
return [
    'question_prefix' => '',

    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    'scoring_legend' => 'Risiko Rendah 0-5 · Risiko Sedang 6-10 · Risiko Tinggi ≥11',

    'tinggi_min' => 11,
    'sedang_min' => 6,

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'Apakah Anda sering merasakan sakit kepala, terutama di bagian belakang kepala', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'Apakah Anda sering merasa pusing atau seperti berputar (vertigo)', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'Apakah Anda mudah lelah atau cepat capek tanpa aktivitas berat', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'Apakah penglihatan Anda terasa kabur atau sulit fokus melihat', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'Apakah hidung Anda mudah berdarah tanpa sebab yang jelas (mimisan)', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'Apakah jantung Anda terasa berdebar atau berdetak tidak teratur', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'Apakah usia Anda sudah 40 tahun atau lebih', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'Apakah ada anggota keluarga dekat Anda yang memiliki riwayat tekanan darah tinggi', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Apakah Anda sering mengonsumsi makanan yang asin atau tinggi garam', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'Apakah Anda jarang berolahraga atau aktivitas fisik Anda rendah', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'Apakah berat badan Anda di atas normal atau Anda mengalami obesitas', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'Apakah Anda aktif merokok atau baru saja berhenti merokok', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'Apakah Anda mengonsumsi alkohol secara rutin', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah Anda sering merasa stres dalam jangka waktu lama', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'Apakah Anda memiliki riwayat diabetes melitus atau kencing manis', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'Apakah Anda pernah didiagnosis mengalami penyakit ginjal', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'Apakah hasil pemeriksaan kolesterol Anda pernah tinggi', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'Apakah Anda pernah didiagnosis mengalami penyakit jantung', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'Apakah tekanan darah Anda pernah terukur 140/90 mmHg atau lebih', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'Apakah Anda pernah didiagnosis hipertensi oleh dokter atau tenaga kesehatan', 'score_ya' => 1],
    ],
];
