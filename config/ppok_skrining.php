<?php

/**
 * Kuesioner skrining PPOK — 19 item gejala/faktor risiko (≥ 3 bulan terakhir).
 * Jawaban: Ya → skor 1 | Tidak → 0
 */
return [
    'question_prefix' => '',

    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    'scoring_legend' => 'Risiko Rendah 0–4 · Risiko Sedang 5–8 · Risiko Tinggi ≥9',

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'Dalam 3 bulan terakhir atau lebih, apakah Anda sering batuk hampir setiap hari', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'Apakah batuk Anda disertai dahak atau lendir dari paru-paru', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'Apakah Anda merasa sesak napas saat melakukan aktivitas ringan, seperti berjalan atau naik tangga', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'Apakah napas Anda terdengar berbunyi mengi (seperti siulan) saat bernapas', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'Apakah Anda mudah lelah atau cepat capek saat beraktivitas', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'Apakah Anda aktif merokok, misalnya minimal 10 batang per hari atau sudah merokok lebih dari 10 tahun', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'Apakah Anda pernah merokok tetapi sudah berhenti (mantan perokok)', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'Apakah Anda sering terpapar asap rokok dari orang lain di sekitar Anda (perokok pasif)', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Apakah Anda sering terpapar asap dapur dari kayu, arang, atau bahan bakar lainnya saat memasak', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'Apakah pekerjaan Anda membuat Anda sering terpapar debu, bahan kimia, atau zat berbahaya lainnya', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'Apakah Anda tinggal di lingkungan dengan polusi udara yang tinggi', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'Apakah Anda sering mengalami infeksi saluran pernapasan atas (ISPA) yang berulang', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'Apakah Anda memiliki riwayat asma', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah Anda pernah didiagnosis mengalami penyakit paru sebelumnya oleh tenaga kesehatan', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'Apakah usia Anda sudah 40 tahun atau lebih', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'Apakah napas Anda terasa cepat, bahkan saat tidak sedang berolahraga', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'Apakah Anda merasa perlu menggunakan otot dada atau leher untuk membantu bernapas', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'Apakah dada Anda terasa berat, sesak, atau seperti tertekan saat bernapas', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'Apakah berat badan Anda turun tanpa sebab yang jelas dalam beberapa bulan terakhir', 'score_ya' => 1],
    ],
];
