<?php

namespace Database\Seeders;

use App\Models\Wilayah;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['kode' => '11', 'nama' => 'Aceh'],
            ['kode' => '11.01', 'nama' => 'Kabupaten Aceh Selatan'],
            ['kode' => '11.01.01', 'nama' => 'Bakongan'],
            ['kode' => '11.01.01.2001', 'nama' => 'Keude Bakongan'],
        ];

        foreach ($rows as $row) {
            Wilayah::updateOrCreate(
                ['kode' => $row['kode']],
                ['nama' => $row['nama']],
            );
        }
    }
}
