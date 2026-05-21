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
        ['id' => 'q01', 'no' => 1, 'text' => 'Batuk lebih dari 2 minggu', 'score_ya' => 3],
        ['id' => 'q02', 'no' => 2, 'text' => 'Demam', 'score_ya' => 2],
        ['id' => 'q03', 'no' => 3, 'text' => 'Berkeringat malam hari tanpa aktivitas', 'score_ya' => 2],
        ['id' => 'q04', 'no' => 4, 'text' => 'Sesak nafas', 'score_ya' => 1],
        ['id' => 'q05', 'no' => 5, 'text' => 'Nyeri dada', 'score_ya' => 1],
        ['id' => 'q06', 'no' => 6, 'text' => 'Ada benjolan di leher/bawah rahang/bawah telinga/ketiak', 'score_ya' => 1],
        ['id' => 'q07', 'no' => 7, 'text' => 'Batuk berdarah', 'score_ya' => 3],
        ['id' => 'q08', 'no' => 8, 'text' => 'Batuk kurang dari 2 minggu', 'score_ya' => 1],
        ['id' => 'q09', 'no' => 9, 'text' => 'Nafsu makan turun', 'score_ya' => 1],
        ['id' => 'q10', 'no' => 10, 'text' => 'Mudah lelah', 'score_ya' => 1],
        ['id' => 'q11', 'no' => 11, 'text' => 'Berat badan turun', 'score_ya' => 2],
        ['id' => 'q12', 'no' => 12, 'text' => 'Anggota keluarga serumah ada yang sakit TBC ?', 'score_ya' => 3],
        ['id' => 'q13', 'no' => 13, 'text' => 'Pernah berada satu ruangan dengan penderita TBC (di kantor, tempat kerja/ kelas/ kamar/ asrama/ panti/ barak, dll) ?', 'score_ya' => 2],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah pernah tinggal serumah minimal satu malam atau sering tinggal serumah pada siang hari dengan orang yang sakit TBC ?', 'score_ya' => 2],
        ['id' => 'q15', 'no' => 15, 'text' => 'Pernah Berobat TBC tuntas', 'score_ya' => 1],
        ['id' => 'q16', 'no' => 16, 'text' => 'Pernah Berobat TBC tapi tidak tuntas', 'score_ya' => 3],
        ['id' => 'q17', 'no' => 17, 'text' => 'Punya riwayat diabetes melitus / kencing manis', 'score_ya' => 2],
        ['id' => 'q18', 'no' => 18, 'text' => 'Orang Dengan HIV', 'score_ya' => 3],
        ['id' => 'q19', 'no' => 19, 'text' => 'Ibu Hamil', 'score_ya' => 1],
        ['id' => 'q20', 'no' => 20, 'text' => 'Merokok', 'score_ya' => 1],
        ['id' => 'q21', 'no' => 21, 'text' => 'Usia 0-14 tahun', 'score_ya' => 1],
        ['id' => 'q22', 'no' => 22, 'text' => 'Kurang Gizi (kurus)', 'score_ya' => 2],
        ['id' => 'q23', 'no' => 23, 'text' => 'Lansia (diatas 60 tahun)', 'score_ya' => 1],
    ],
];
