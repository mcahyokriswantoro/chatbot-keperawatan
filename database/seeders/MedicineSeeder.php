<?php

namespace Database\Seeders;

use App\Models\Medicine;
use Illuminate\Database\Seeder;

class MedicineSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // Obat Bebas
            [
                'name' => 'Paracetamol 500 mg (1 Strip)',
                'category' => 'Obat Bebas',
                'price' => 5000,
                'stock' => 100,
                'description' => 'Meredakan demam, sakit kepala, dan nyeri ringan.',
                'photo' => null,
                'active' => true,
            ],
            [
                'name' => 'Sanmol Sirup 60 ml',
                'category' => 'Obat Bebas',
                'price' => 20000,
                'stock' => 50,
                'description' => 'Sirup penurun demam dan pereda nyeri untuk anak.',
                'photo' => null,
                'active' => true,
            ],
            [
                'name' => 'Bodrex (1 Strip)',
                'category' => 'Obat Bebas',
                'price' => 4000,
                'stock' => 120,
                'description' => 'Meredakan sakit kepala dan sakit gigi.',
                'photo' => null,
                'active' => true,
            ],
            [
                'name' => 'OBH Combi Batuk & Flu 100 ml',
                'category' => 'Obat Bebas',
                'price' => 18000,
                'stock' => 40,
                'description' => 'Meredakan batuk berdahak disertai gejala flu.',
                'photo' => null,
                'active' => true,
            ],

            // Vitamin & Suplemen
            [
                'name' => 'Enervon-C (30 Tablet)',
                'category' => 'Vitamin & Suplemen',
                'price' => 40000,
                'stock' => 80,
                'description' => 'Suplemen vitamin C untuk menjaga daya tahan tubuh.',
                'photo' => null,
                'active' => true,
            ],
            [
                'name' => 'CDR Effervescent (10 Tablet)',
                'category' => 'Vitamin & Suplemen',
                'price' => 45000,
                'stock' => 60,
                'description' => 'Kalsium effervescent dengan vitamin C, D, dan B6.',
                'photo' => null,
                'active' => true,
            ],
            [
                'name' => 'Sangobion (10 Kapsul)',
                'category' => 'Vitamin & Suplemen',
                'price' => 22000,
                'stock' => 90,
                'description' => 'Suplemen zat besi dan vitamin penambah darah.',
                'photo' => null,
                'active' => true,
            ],

            // Obat Keras
            [
                'name' => 'Amoxicillin 500 mg (1 Strip)',
                'category' => 'Obat Keras',
                'price' => 15000,
                'stock' => 50,
                'description' => 'Antibiotik untuk mengobati infeksi bakteri. Harus dengan resep dokter.',
                'photo' => null,
                'active' => true,
            ],
            [
                'name' => 'Cefadroxil 500 mg (1 Strip)',
                'category' => 'Obat Keras',
                'price' => 25000,
                'stock' => 30,
                'description' => 'Antibiotik cephalosporin untuk infeksi bakteri. Harus dengan resep dokter.',
                'photo' => null,
                'active' => true,
            ],
        ];

        foreach ($items as $item) {
            Medicine::updateOrCreate(
                ['name' => $item['name']],
                $item
            );
        }
    }
}
