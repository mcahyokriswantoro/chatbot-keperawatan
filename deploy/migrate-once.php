<?php

declare(strict_types=1);

/**
 * Jalankan migration database via browser.
 * Upload ke public_html/migrate-once.php (atau public/)
 * Buka: https://chatbot-keperawatan.damgocompany.com/migrate-once.php?key=ck2026migrate
 * HAPUS file setelah SELESAI.
 */
const MIGRATE_KEY = 'ck2026migrate';

if (($_GET['key'] ?? '') !== MIGRATE_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');
set_time_limit(300);

define('LARAVEL_START', microtime(true));

$root = dirname(__DIR__);

require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== MIGRATION v14 ===\n\n";

try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo 'Database: '.env('DB_DATABASE')." — OK\n\n";
} catch (Throwable $e) {
    http_response_code(500);
    exit("ERROR koneksi DB:\n".$e->getMessage());
}

$checks = [
    'monitor_type column' => \Illuminate\Support\Facades\Schema::hasColumn('health_monitorings', 'monitor_type'),
    'medication_checks column' => \Illuminate\Support\Facades\Schema::hasColumn('health_monitorings', 'medication_checks'),
    'user_medications table' => \Illuminate\Support\Facades\Schema::hasTable('user_medications'),
];

echo "=== SEBELUM MIGRATE ===\n";
foreach ($checks as $label => $ok) {
    echo ($ok ? 'OK' : 'BELUM')." — {$label}\n";
}
echo "\n";

echo ">>> php artisan migrate --force\n";
$status = $kernel->call('migrate', ['--force' => true]);
echo trim((string) $kernel->output())."\n";
echo "Exit: {$status}\n\n";

if ($status !== 0) {
    http_response_code(500);
    exit("GAGAL migrate. Cek storage/logs/laravel.log\n");
}

foreach (['config:clear', 'route:clear', 'view:clear', 'cache:clear'] as $cmd) {
    echo ">>> php artisan {$cmd}\n";
    $kernel->call($cmd);
    echo trim((string) $kernel->output())."\n\n";
}

echo "=== SETELAH MIGRATE ===\n";
foreach ($checks as $label => $_) {
    $ok = match ($label) {
        'monitor_type column' => \Illuminate\Support\Facades\Schema::hasColumn('health_monitorings', 'monitor_type'),
        'medication_checks column' => \Illuminate\Support\Facades\Schema::hasColumn('health_monitorings', 'medication_checks'),
        'user_medications table' => \Illuminate\Support\Facades\Schema::hasTable('user_medications'),
        default => false,
    };
    echo ($ok ? 'OK' : 'BELUM')." — {$label}\n";
}

echo "\nSELESAI. Hapus migrate-once.php lalu buka /admin.\n";
