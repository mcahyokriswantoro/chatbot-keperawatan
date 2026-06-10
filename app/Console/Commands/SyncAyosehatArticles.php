<?php

namespace App\Console\Commands;

use App\Services\AyosehatSyncService;
use App\Services\HealthTipService;
use Illuminate\Console\Command;

class SyncAyosehatArticles extends Command
{
    protected $signature = 'ayosehat:sync';

    protected $description = 'Sinkronkan artikel edukasi dari ayosehat.kemkes.go.id';

    public function handle(AyosehatSyncService $syncService, HealthTipService $healthTipService): int
    {
        $this->info('Memulai sinkronisasi artikel Ayo Sehat...');

        $result = $syncService->sync();

        $this->info("Berhasil sync {$result['synced']} artikel.");

        if ($result['deactivated'] > 0) {
            $this->line("Nonaktifkan {$result['deactivated']} artikel yang tidak lagi ada di sumber.");
        }

        foreach ($result['errors'] as $error) {
            $this->error($error);
        }

        $tipCount = $healthTipService->refreshFromArticles();
        if ($tipCount > 0) {
            $this->info("Tips mingguan diperbarui ({$tipCount} tips).");
        }

        return $result['errors'] === [] || $result['synced'] > 0
            ? self::SUCCESS
            : self::FAILURE;
    }
}
