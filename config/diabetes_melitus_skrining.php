<?php

/**
 * Kuesioner skrining Diabetes Melitus — 23 item gejala dan faktor risiko.
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
        ['id' => 'q01', 'no' => 1, 'text' => 'Apakah Anda sering buang air kecil, terutama di malam hari', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'Apakah Anda sering merasa haus dan ingin minum terus-menerus', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'Apakah Anda sering merasa lapar meskipun baru saja makan', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'Apakah berat badan Anda turun tanpa diet atau olahraga tertentu', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'Apakah Anda mudah lelah atau merasa lemas sepanjang hari', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'Apakah penglihatan Anda terasa kabur atau sulit fokus melihat', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'Apakah luka kecil di kulit Anda sulit sembuh atau lama kering', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'Apakah Anda sering mengalami infeksi, seperti infeksi kulit, gusi, atau saluran kemih', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Apakah Anda merasakan kesemutan atau mati rasa di tangan atau kaki', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'Apakah usia Anda sudah 40 tahun atau lebih', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'Apakah ada anggota keluarga dekat Anda yang memiliki diabetes', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'Apakah berat badan Anda di atas normal atau Anda mengalami obesitas', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'Apakah Anda jarang berolahraga atau aktivitas fisik Anda rendah', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah pola makan Anda sering tinggi gula, manis-manis, atau makanan berlemak', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'Apakah Anda memiliki riwayat tekanan darah tinggi (hipertensi)', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'Apakah hasil pemeriksaan kolesterol Anda pernah tinggi', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'Apakah Anda pernah didiagnosis diabetes saat hamil (diabetes gestasional)', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'Apakah Anda pernah melahirkan bayi dengan berat lahir lebih dari 4 kg', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'Apakah Anda pernah didiagnosis prediabetes oleh tenaga kesehatan', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'Apakah lingkar perut Anda besar atau Anda memiliki obesitas sentral (perut buncit)', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'Apakah hasil gula darah puasa Anda pernah 100 mg/dL atau lebih', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'Apakah hasil gula darah sewaktu Anda pernah 200 mg/dL atau lebih', 'score_ya' => 1],
        ['id' => 'q23', 'no' => 23, 'text' => 'Apakah hasil pemeriksaan HbA1c Anda pernah 5,7% atau lebih', 'score_ya' => 1],
    ],
];
