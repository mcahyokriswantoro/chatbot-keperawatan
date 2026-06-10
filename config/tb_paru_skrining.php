<?php

/**
 * Kuesioner skrining TB Paru — 23 item baku.
 * Jawaban: Ya → skor sesuai kolom score_ya | Tidak → 0
 */
return [
    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'Apakah Anda mengalami batuk yang sudah berlangsung lebih dari dua minggu dan belum membaik', 'score_ya' => 3],
        ['id' => 'q02', 'no' => 2, 'text' => 'Apakah Anda sedang atau baru saja mengalami demam', 'score_ya' => 2],
        ['id' => 'q03', 'no' => 3, 'text' => 'Apakah Anda sering berkeringat di malam hari meskipun tidak sedang beraktivitas', 'score_ya' => 2],
        ['id' => 'q04', 'no' => 4, 'text' => 'Apakah Anda merasa sesak napas atau sulit bernapas', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'Apakah Anda merasakan nyeri atau tidak nyaman di area dada', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'Apakah Anda menemukan benjolan di leher, bawah rahang, ketiak, atau di bawah telinga', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'Apakah Anda pernah batuk dan keluar darah', 'score_ya' => 3],
        ['id' => 'q08', 'no' => 8, 'text' => 'Apakah Anda mengalami batuk, meskipun belum sampai dua minggu', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Apakah nafsu makan Anda menurun dibanding biasanya', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'Apakah Anda mudah merasa lelah meskipun aktivitasnya ringan', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'Apakah berat badan Anda turun tanpa usaha diet atau olahraga tertentu', 'score_ya' => 2],
        ['id' => 'q12', 'no' => 12, 'text' => 'Apakah ada anggota keluarga yang tinggal serumah dengan Anda yang sedang atau pernah sakit TBC', 'score_ya' => 3],
        ['id' => 'q13', 'no' => 13, 'text' => 'Apakah Anda pernah berada dalam satu ruangan yang sama dengan penderita TBC, misalnya di kantor, kelas, kamar, atau asrama', 'score_ya' => 2],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah Anda pernah tinggal serumah minimal satu malam, atau sering berada di rumah yang sama pada siang hari, dengan orang yang sakit TBC', 'score_ya' => 2],
        ['id' => 'q15', 'no' => 15, 'text' => 'Apakah Anda pernah menjalani pengobatan TBC sampai tuntas', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'Apakah Anda pernah berobat karena TBC, tetapi pengobatannya tidak selesai atau berhenti di tengah jalan', 'score_ya' => 3],
        ['id' => 'q17', 'no' => 17, 'text' => 'Apakah Anda memiliki riwayat diabetes melitus atau kencing manis', 'score_ya' => 2],
        ['id' => 'q18', 'no' => 18, 'text' => 'Apakah Anda termasuk orang dengan HIV atau infeksi HIV', 'score_ya' => 3],
        ['id' => 'q19', 'no' => 19, 'text' => 'Apakah Anda sedang hamil', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'Apakah Anda aktif merokok', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'Apakah usia Anda antara 0 sampai 14 tahun', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'Apakah Anda mengalami kekurangan gizi atau tampak kurus', 'score_ya' => 2],
        ['id' => 'q23', 'no' => 23, 'text' => 'Apakah usia Anda di atas 60 tahun (lansia)', 'score_ya' => 1],
    ],
];
