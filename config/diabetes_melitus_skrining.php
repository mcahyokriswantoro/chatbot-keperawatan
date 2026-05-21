<?php

/**
 * Kuesioner skrining Diabetes Melitus — 23 item gejala dan faktor risiko.
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
        ['id' => 'q01', 'no' => 1, 'text' => 'sering buang air kecil (poliuria)', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'sering merasa haus (polidipsia)', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'sering merasa lapar (polifagia)', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'penurunan berat badan tanpa sebab jelas', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'mudah lelah / lemas', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'penglihatan kabur', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'luka sulit sembuh', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'sering infeksi (kulit, gusi, saluran kemih)', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'kesemutan pada tangan/kaki', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'usia ≥ 40 tahun', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'riwayat keluarga dengan diabetes', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'berat badan berlebih / obesitas', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'kurang aktivitas fisik', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'pola makan tinggi gula/lemak', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'riwayat Hipertensi', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'riwayat kolesterol tinggi', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'riwayat diabetes saat hamil (gestasional)', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'riwayat bayi lahir > 4 kg', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'pernah didiagnosis prediabetes', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'lingkar perut berlebih (obesitas sentral)', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'gula darah puasa ≥ 100 mg/dL', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'gula darah sewaktu ≥ 200 mg/dL', 'score_ya' => 1],
        ['id' => 'q23', 'no' => 23, 'text' => 'HbA1c ≥ 5,7%', 'score_ya' => 1],
    ],
];
