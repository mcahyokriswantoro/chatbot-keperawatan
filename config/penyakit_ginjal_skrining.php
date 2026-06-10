<?php

/**
 * Kuesioner skrining Penyakit Ginjal — 26 item.
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
        ['id' => 'q01', 'no' => 1, 'text' => 'Dalam beberapa minggu atau bulan terakhir, apakah Anda sering merasa lelah tanpa alasan yang jelas', 'score_ya' => 1],
        ['id' => 'q02', 'no' => 2, 'text' => 'Apakah nafsu makan Anda menurun dan Anda jadi jarang ingin makan', 'score_ya' => 1],
        ['id' => 'q03', 'no' => 3, 'text' => 'Apakah Anda sering merasa mual atau pernah muntah', 'score_ya' => 1],
        ['id' => 'q04', 'no' => 4, 'text' => 'Apakah kulit Anda terasa gatal-gatal tanpa ruam yang jelas', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'Apakah ada pembengkakan di kaki, tangan, atau wajah Anda', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'Apakah Anda merasa sesak napas, terutama saat beraktivitas atau berbaring', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'Apakah Anda merasakan nyeri di area pinggang atau sekitar ginjal', 'score_ya' => 1],
        ['id' => 'q08', 'no' => 8, 'text' => 'Apakah Anda sering mengalami kram otot, terutama di malam hari', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Apakah frekuensi buang air kecil Anda meningkat, terutama di malam hari', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'Apakah jumlah urine yang keluar terasa berkurang dibanding biasanya', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'Apakah urine Anda berbusa atau berbuih banyak, seperti sabun', 'score_ya' => 1],
        ['id' => 'q12', 'no' => 12, 'text' => 'Apakah warna urine Anda gelap, keruh, atau ada darah di dalamnya', 'score_ya' => 1],
        ['id' => 'q13', 'no' => 13, 'text' => 'Apakah Anda merasakan nyeri atau tidak nyaman saat buang air kecil', 'score_ya' => 1],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah Anda memiliki riwayat diabetes melitus atau kencing manis', 'score_ya' => 1],
        ['id' => 'q15', 'no' => 15, 'text' => 'Apakah Anda memiliki riwayat tekanan darah tinggi (hipertensi)', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'Apakah Anda pernah didiagnosis mengalami penyakit jantung', 'score_ya' => 1],
        ['id' => 'q17', 'no' => 17, 'text' => 'Apakah ada anggota keluarga dekat Anda yang memiliki riwayat penyakit ginjal', 'score_ya' => 1],
        ['id' => 'q18', 'no' => 18, 'text' => 'Apakah usia Anda sudah 40 tahun atau lebih', 'score_ya' => 1],
        ['id' => 'q19', 'no' => 19, 'text' => 'Apakah Anda rutin mengonsumsi obat dalam jangka panjang, seperti obat pereda nyeri atau obat herbal tertentu', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'Apakah Anda sering mengalami infeksi saluran kemih yang berulang', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'Apakah Anda pernah mengalami batu ginjal', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'Apakah Anda pernah mengalami dehidrasi berat (kekurangan cairan) secara berulang', 'score_ya' => 1],
        ['id' => 'q23', 'no' => 23, 'text' => 'Apakah tekanan darah Anda pernah terukur tinggi, di atas 140/90 mmHg', 'score_ya' => 1],
        ['id' => 'q24', 'no' => 24, 'text' => 'Apakah hasil pemeriksaan gula darah Anda pernah tinggi', 'score_ya' => 1],
        ['id' => 'q25', 'no' => 25, 'text' => 'Apakah hasil pemeriksaan kreatinin darah Anda pernah menunjukkan nilai meningkat', 'score_ya' => 1],
        ['id' => 'q26', 'no' => 26, 'text' => 'Apakah hasil pemeriksaan urine Anda pernah menunjukkan adanya protein (proteinuria positif)', 'score_ya' => 1],
    ],
];
