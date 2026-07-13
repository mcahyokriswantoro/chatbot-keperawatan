<?php

return [
    'severity_options' => [
        ['value' => 'tidak_ada', 'label' => 'Tidak ada', 'score' => 0],
        ['value' => 'ringan', 'label' => 'Ringan', 'score' => 1],
        ['value' => 'sedang', 'label' => 'Sedang', 'score' => 2],
        ['value' => 'berat', 'label' => 'Berat', 'score' => 3],
    ],

    'self_management_options' => [
        ['value' => 'tidak', 'label' => 'Tidak', 'score' => 0],
        ['value' => 'sepenuhnya', 'label' => 'Ya', 'score' => 2],
    ],

    'relapse_options' => [
        ['value' => 'tidak_pernah', 'label' => 'Tidak pernah kambuh', 'score' => 0],
        ['value' => '1_kali', 'label' => 'Sekali', 'score' => 1],
        ['value' => '2_kali', 'label' => 'Lebih dari 2 kali', 'score' => 2],
        ['value' => '3_kali', 'label' => 'Lebih dari 3 kali', 'score' => 3],
    ],

    'score_labels' => [
        'baik' => 'Baik',
        'cukup' => 'Cukup',
        'kurang' => 'Kurang',
    ],

    /**
     * Keluhan: semakin tinggi skor semakin buruk → invert percent.
     */
    'complaint_thresholds' => [
        ['max_percent' => 25, 'label' => 'baik'],
        ['max_percent' => 50, 'label' => 'cukup'],
        ['max_percent' => 100, 'label' => 'kurang'],
    ],

    /**
     * Self management & kepatuhan obat: semakin tinggi persen semakin baik.
     */
    'percent_thresholds' => [
        ['min' => 80, 'label' => 'baik'],
        ['min' => 60, 'label' => 'cukup'],
        ['min' => 0, 'label' => 'kurang'],
    ],

    'relapse_labels' => [
        0 => 'baik',
        1 => 'cukup',
        2 => 'kurang',
        3 => 'kurang',
    ],
];
