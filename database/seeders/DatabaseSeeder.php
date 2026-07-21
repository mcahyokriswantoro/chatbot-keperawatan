<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(HealthFeatureSeeder::class);
        $this->call(ConsultationVoucherSeeder::class);
        $this->call(ConsultationProviderSeeder::class);
        $this->call(MedicineSeeder::class);
        $this->call(HomecarePackageSeeder::class);
    }
}
