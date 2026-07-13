<?php

/**
 * Skrining awal — 20 pertanyaan ya/tidak dengan routing ke skrining lanjut.
 */
return [
    'yes_no_options' => [
        ['value' => 'ya', 'label' => 'Ya'],
        ['value' => 'tidak', 'label' => 'Tidak'],
    ],

    'disease_order' => [
        'tb_paru',
        'dhf',
        'ppok',
        'penyakit_ginjal',
        'stroke',
        'jantung_koroner',
        'diabetes_melitus',
        'hipertensi',
        'rheumatoid_arthritis',
    ],

    /**
     * Jika jawaban ya pada pertanyaan id → tambahkan skrining lanjut berikut.
     *
     * @var array<string, list<string>>
     */
    'routes' => [
        'q01' => ['stroke', 'jantung_koroner', 'diabetes_melitus', 'rheumatoid_arthritis'],
        'q02' => ['ppok', 'jantung_koroner', 'rheumatoid_arthritis'],
        'q02a' => ['hipertensi'],
        'q03' => ['jantung_koroner', 'diabetes_melitus', 'hipertensi'],
        'q04' => ['hipertensi'],
        'q05' => ['stroke', 'diabetes_melitus'],
        'q06' => ['stroke', 'jantung_koroner', 'hipertensi'],
        'q07' => ['stroke', 'jantung_koroner'],
        'q08' => ['tb_paru'],
        'q09' => ['ppok'],
        'q10' => ['tb_paru', 'ppok'],
        'q11' => ['tb_paru'],
        'q12' => ['tb_paru'],
        'q13' => ['dhf'],
        'q14' => ['dhf'],
        'q15' => ['diabetes_melitus'],
        'q16' => ['jantung_koroner'],
        'q17' => ['jantung_koroner', 'diabetes_melitus', 'hipertensi'],
        'q18' => ['stroke'],
        'q19' => ['penyakit_ginjal'],
        'q20' => ['rheumatoid_arthritis'],
    ],

    'items' => [
        ['id' => 'q01', 'no' => 1, 'text' => 'Apakah Anda usia lebih dari 40 tahun'],
        ['id' => 'q02', 'no' => 2, 'text' => 'Apakah Anda merokok atau pernah merokok'],
        ['id' => 'q02a', 'no' => '2a', 'text' => 'Apakah Anda sering konsumsi asin'],
        ['id' => 'q03', 'no' => 3, 'text' => 'Apakah Anda memiliki berat badan berlebih (obesitas)'],
        ['id' => 'q04', 'no' => 4, 'text' => 'Apakah Anda jarang berolahraga (kurang dari 150 menit/minggu)'],
        ['id' => 'q05', 'no' => 5, 'text' => 'Apakah ada anggota keluarga yang memiliki diabetes, hipertensi, stroke, penyakit jantung, atau penyakit ginjal'],
        ['id' => 'q06', 'no' => 6, 'text' => 'Apakah Anda pernah didiagnosis hipertensi'],
        ['id' => 'q07', 'no' => 7, 'text' => 'Apakah Anda pernah didiagnosis diabetes melitus'],
        ['id' => 'q08', 'no' => 8, 'text' => 'Apakah Anda mengalami batuk selama lebih dari 2 minggu'],
        ['id' => 'q09', 'no' => 9, 'text' => 'Apakah Anda sering sesak napas'],
        ['id' => 'q10', 'no' => 10, 'text' => 'Apakah Anda sering mengeluarkan dahak atau lendir'],
        ['id' => 'q11', 'no' => 11, 'text' => 'Apakah berat badan Anda turun tanpa sebab yang jelas dalam 3 bulan terakhir'],
        ['id' => 'q12', 'no' => 12, 'text' => 'Apakah Anda pernah kontak erat dengan penderita TB'],
        ['id' => 'q13', 'no' => 13, 'text' => 'Apakah Anda mengalami demam tinggi mendadak dalam 7 hari terakhir'],
        ['id' => 'q14', 'no' => 14, 'text' => 'Apakah muncul bintik merah, mimisan, atau perdarahan lainnya saat demam'],
        ['id' => 'q15', 'no' => 15, 'text' => 'Apakah Anda sering haus dan sering buang air kecil'],
        ['id' => 'q16', 'no' => 16, 'text' => 'Apakah Anda sering mengalami nyeri dada saat aktivitas'],
        ['id' => 'q17', 'no' => 17, 'text' => 'Apakah Anda mudah lelah atau sesak saat aktivitas ringan'],
        ['id' => 'q18', 'no' => 18, 'text' => 'Apakah Anda pernah mengalami wajah mencong, bicara pelo, atau kelemahan salah satu sisi tubuh secara mendadak'],
        ['id' => 'q19', 'no' => 19, 'text' => 'Apakah kaki atau wajah Anda sering bengkak, atau urin berbusa'],
        ['id' => 'q20', 'no' => 20, 'text' => 'Apakah Anda mengalami nyeri sendi dan kaku pada pagi hari lebih dari 30 menit, atau nyeri sendi simetris kanan-kiri'],
    ],
];
