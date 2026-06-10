<?php

return [
    'emergency' => [
        'title' => 'Segera ke rumah sakit jika Anda mengalami:',
        'items' => [
            'Batuk yang keluar darah',
            'Sesak napas yang sangat berat',
            'Penurunan berat badan yang drastis dalam waktu singkat',
            'Demam tinggi yang tidak turun-turun',
        ],
    ],
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'intro' => 'Hasil skrining menunjukkan risiko rendah. Tetap jaga kesehatan dan waspadai gejala baru.',
        'sections' => [
            ['title' => 'Hidup Sehat Sehari-hari', 'items' => [
                'Makan makanan bergizi, terutama yang tinggi protein seperti telur, ikan, dan tempe',
                'Istirahat cukup, sekitar 7–8 jam setiap malam',
                'Lakukan olahraga ringan seperti jalan kaki atau senam',
            ]],
            ['title' => 'Cegah Penularan TB', 'items' => [
                'Hindari kontak dekat dengan orang yang sedang sakit TB aktif',
                'Pakai masker jika berada di lingkungan yang berisiko',
                'Saat batuk atau bersin, tutup mulut dengan tisu atau lengan',
            ]],
            ['title' => 'Jaga Lingkungan Rumah', 'items' => [
                'Pastikan rumah memiliki ventilasi udara yang baik',
                'Biarkan sinar matahari masuk ke dalam rumah',
                'Hindari ruangan yang terlalu padat penghuninya',
            ]],
            ['title' => 'Pantau Kondisi Anda', 'items' => [
                'Ulangi skrining jika muncul gejala baru',
                'Ajak keluarga ikut belajar tentang TB',
            ]],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'intro' => 'Ada beberapa tanda yang perlu diwaspadai. Segera periksa ke fasilitas kesehatan agar bisa ditangani lebih awal.',
        'sections' => [
            ['title' => 'Deteksi Dini', 'items' => [
                'Segera periksa ke puskesmas atau klinik terdekat',
                'Jika batuk sudah 2 minggu atau lebih, minta pemeriksaan dahak',
            ]],
            ['title' => 'Lindungi Orang di Sekitar', 'items' => [
                'Pakai masker saat batuk',
                'Tutup mulut saat batuk atau bersin',
                'Jangan berbagi alat makan dengan orang lain',
            ]],
            ['title' => 'Perkuat Tubuh dengan Nutrisi', 'items' => [
                'Perbanyak protein serta vitamin A, C, dan D',
                'Minum air putih yang cukup setiap hari',
            ]],
            ['title' => 'Catat Gejala yang Muncul', 'items' => [
                'Berapa lama batuk berlangsung',
                'Apakah berat badan turun',
                'Apakah ada demam atau keringat malam',
            ]],
            ['title' => 'Libatkan Keluarga', 'items' => [
                'Ajak anggota keluarga serumah untuk diperiksa jika ada gejala serupa',
                'Bantu keluarga memahami bahaya dan pencegahan TB',
            ]],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi',
        'intro' => 'Hasil skrining menunjukkan risiko tinggi. Periksa ke fasilitas kesehatan sesegera mungkin dan ikuti anjuran pengobatan dari tenaga medis.',
        'sections' => [
            ['title' => 'Tindakan yang Perlu Segera Dilakukan', 'items' => [
                'Wajib periksa ke fasilitas kesehatan',
                'Lakukan tes dahak (BTA/TCM) sesuai anjuran dokter',
                'Lakukan rontgen dada jika diperlukan',
            ]],
            ['title' => 'Minum Obat dengan Teratur', 'items' => [
                'Ikuti program pengobatan TB (DOTS) selama 6–9 bulan',
                'Jangan berhenti minum obat tanpa izin dokter',
                'Usahakan minum obat pada jam yang sama setiap hari',
            ]],
            ['title' => 'Cegah Menular ke Orang Lain', 'items' => [
                'Pakai masker setiap hari',
                'Tidur terpisah sementara dari anggota keluarga jika memungkinkan',
                'Buka jendela agar udara segar bisa masuk',
                'Tutup mulut dengan tisu atau siku saat batuk',
            ]],
            ['title' => 'Dukung Pemulihan dengan Makanan', 'items' => [
                'Perbanyak makanan tinggi protein seperti ikan, ayam, dan telur',
                'Pastikan asupan kalori cukup',
                'Perhatikan kebutuhan vitamin dan mineral',
            ]],
            ['title' => 'Kelola Kondisi Bersama Keluarga', 'items' => [
                'Ajak seluruh anggota keluarga untuk skrining TB',
                'Edukasi keluarga tentang pengobatan dan pencegahan',
                'Batasi kontak dekat dengan anak kecil dan lansia',
            ]],
            ['title' => 'Pantau Kondisi Setiap Hari', 'items' => [
                'Catat apakah obat sudah diminum tepat waktu',
                'Timbang berat badan secara berkala',
                'Perhatikan batuk, demam, dan nafsu makan',
            ]],
        ],
    ],
];
