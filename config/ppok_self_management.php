<?php

return [
    'emergency' => [
        'title' => 'Segera ke rumah sakit jika Anda mengalami:',
        'items' => [
            'Sesak napas berat yang tiba-tiba',
            'Napas sangat cepat dan sulit berbicara',
            'Bibir atau kuku kebiruan (sianosis)',
            'Penurunan kesadaran',
        ],
    ],
    'Rendah' => [
        'label' => 'Risiko Rendah',
        'intro' => 'Paru-paru Anda relatif baik, tetapi tetap jaga kebiasaan sehat agar kondisi tidak memburuk.',
        'sections' => [
            ['title' => 'Hindari Rokok dan Asap', 'items' => [
                'Berhenti merokok jika Anda masih merokok',
                'Jauhi asap rokok orang lain (perokok pasif)',
            ]],
            ['title' => 'Lindungi dari Polusi', 'items' => [
                'Pakai masker di lingkungan berdebu atau berpolusi',
                'Kurangi paparan asap dapur dari kayu atau arang',
            ]],
            ['title' => 'Gerakkan Tubuh Secara Teratur', 'items' => [
                'Jalan kaki 20–30 menit setiap hari',
                'Coba latihan pernapasan sederhana',
            ]],
            ['title' => 'Makan Bergizi', 'items' => [
                'Pilih makanan tinggi protein dan antioksidan',
                'Minum air putih yang cukup',
            ]],
            ['title' => 'Pantau Gejala', 'items' => [
                'Ulangi skrining jika batuk kronis atau sesak napas muncul',
            ]],
        ],
    ],
    'Sedang' => [
        'label' => 'Risiko Sedang',
        'intro' => 'Ada tanda yang perlu diperhatikan. Konsultasikan ke tenaga kesehatan untuk evaluasi lebih lanjut.',
        'sections' => [
            ['title' => 'Periksa ke Fasilitas Kesehatan', 'items' => [
                'Konsultasi ke puskesmas atau klinik',
                'Tanyakan kemungkinan pemeriksaan spirometri',
            ]],
            ['title' => 'Latihan Pernapasan', 'items' => [
                'Tarik napas dari hidung, hembuskan perlahan lewat mulut (pursed-lip breathing)',
                'Latihan pernapasan diafragma sesuai anjuran tenaga kesehatan',
            ]],
            ['title' => 'Aktivitas Fisik Terukur', 'items' => [
                'Jalan kaki rutin sesuai kemampuan',
                'Hindari aktivitas berat yang melelahkan',
                'Ikuti program rehabilitasi paru jika tersedia',
            ]],
            ['title' => 'Bantu Keluarkan Dahak', 'items' => [
                'Minum cukup cairan agar dahak tidak kental',
                'Pelajari teknik batuk yang efektif',
            ]],
            ['title' => 'Hindari Pemicu Sesak', 'items' => [
                'Asap rokok',
                'Debu dan polusi udara',
                'Udara dingin ekstrem',
            ]],
            ['title' => 'Lindungi Diri dengan Vaksin', 'items' => [
                'Vaksin influenza',
                'Vaksin pneumonia sesuai anjuran dokter',
            ]],
        ],
    ],
    'Tinggi' => [
        'label' => 'Risiko Tinggi',
        'intro' => 'Kondisi paru perlu penanganan medis rutin. Patuhi terapi dan kontrol ke dokter secara berkala.',
        'sections' => [
            ['title' => 'Kontrol Rutin ke Dokter', 'items' => [
                'Jangan melewatkan jadwal kontrol',
                'Lakukan evaluasi fungsi paru (spirometri)',
                'Gunakan inhaler sesuai resep dokter',
            ]],
            ['title' => 'Minum Obat dengan Disiplin', 'items' => [
                'Gunakan inhaler secara teratur',
                'Jangan berhenti obat tanpa berkonsultasi dengan dokter',
            ]],
            ['title' => 'Rehabilitasi Paru', 'items' => [
                'Ikuti latihan napas yang dianjurkan',
                'Lakukan latihan fisik terstruktur',
                'Pelajari cara menghemat energi saat beraktivitas',
            ]],
            ['title' => 'Atur Aktivitas Harian', 'items' => [
                'Istirahat cukup',
                'Bagi aktivitas berat menjadi bagian-bagian kecil',
            ]],
            ['title' => 'Nutrisi untuk Pemulihan', 'items' => [
                'Perbanyak protein dan kalori yang cukup',
                'Pantau berat badan agar tidak malnutrisi',
            ]],
            ['title' => 'Cegah Infeksi', 'items' => [
                'Pakai masker saat di tempat ramai',
                'Cuci tangan secara rutin',
                'Hindari kerumunan saat sedang kurang fit',
            ]],
            ['title' => 'Dukungan dari Keluarga', 'items' => [
                'Ajak keluarga memahami kondisi PPOK',
                'Minta bantuan untuk aktivitas harian jika diperlukan',
            ]],
        ],
    ],
];
