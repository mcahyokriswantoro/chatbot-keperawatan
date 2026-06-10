<?php

namespace App\Console\Commands;

use App\Services\HealthTipService;
use Illuminate\Console\Command;

class RefreshHealthTips extends Command
{
    protected $signature = 'health-tips:refresh';

    protected $description = 'Perbarui tips kesehatan mingguan dari artikel Ayo Sehat';

    public function handle(HealthTipService $healthTipService): int
    {
        $this->info('Memperbarui tips kesehatan minggu ini...');

        $count = $healthTipService->refreshFromArticles();

        if ($count === 0) {
            $this->warn('Tidak ada artikel aktif. Jalankan ayosehat:sync terlebih dahulu.');

            return self::FAILURE;
        }

        $this->info("Berhasil memperbarui {$count} tips untuk minggu ini.");

        return self::SUCCESS;
    }
}
