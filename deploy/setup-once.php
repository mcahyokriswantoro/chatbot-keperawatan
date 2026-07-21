<?php

declare(strict_types=1);

/**
 * Perbaikan otomatis setelah deploy — jalankan sekali via browser.
 * Upload ke public/ DAN public_html/, buka:
 * https://chatbot-keperawatan.damgocompany.com/setup-once.php?key=ck2026setup
 * HAPUS file ini setelah muncul SELESAI.
 */
const SETUP_KEY = 'ck2026setup';

if (($_GET['key'] ?? '') !== SETUP_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');
set_time_limit(300);
ini_set('display_errors', '1');
error_reporting(E_ALL);

define('LARAVEL_START', microtime(true));

$root = dirname(__DIR__);

function ensureDir(string $path): void
{
    if (! is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

function ensureStorageSymlink(string $linkPath, string $targetPath): void
{
    $targetPath = rtrim($targetPath, '/');

    if (is_link($linkPath)) {
        $current = readlink($linkPath) ?: '';
        if ($current === $targetPath || realpath($linkPath) === realpath($targetPath)) {
            echo "OK — {$linkPath} sudah ter-link\n";

            return;
        }
        unlink($linkPath);
    }

    if (is_dir($linkPath) && ! is_link($linkPath)) {
        $files = scandir($linkPath) ?: [];
        $files = array_diff($files, ['.', '..']);
        if ($files === []) {
            rmdir($linkPath);
        } else {
            echo "PERINGATAN — {$linkPath} adalah folder (bukan symlink). Buat manual jika video tidak bisa diputar.\n";

            return;
        }
    }

    if (! is_dir($targetPath)) {
        ensureDir($targetPath);
    }

    if (@symlink($targetPath, $linkPath)) {
        echo "OK — symlink dibuat: {$linkPath}\n";
    } else {
        echo "GAGAL — tidak bisa buat symlink {$linkPath}. Buat manual di File Manager.\n";
    }
}

foreach ([
    $root.'/storage/framework/views',
    $root.'/storage/framework/cache/data',
    $root.'/storage/framework/sessions',
    $root.'/storage/logs',
    $root.'/storage/app/public',
    $root.'/storage/app/public/article-videos',
    $root.'/storage/app/public/article-covers',
    $root.'/storage/app/public/consultation-providers',
    $root.'/storage/app/public/consultation-payment-proofs',
    $root.'/bootstrap/cache',
] as $dir) {
    ensureDir($dir);
}

echo "=== HAPUS CACHE LAMA ===\n";
$cacheKeep = ['packages.php', 'services.php', '.gitignore'];
foreach (glob($root.'/bootstrap/cache/*.php') ?: [] as $file) {
    $base = basename($file);
    if (in_array($base, $cacheKeep, true)) {
        continue;
    }
    if (@unlink($file)) {
        echo "- bootstrap/cache/{$base}\n";
    }
}
echo "\n";

require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$envPath = $root.'/.env';

if (! is_file($envPath)) {
    http_response_code(500);
    exit("ERROR: .env tidak ditemukan di {$root}\n");
}

$envContents = (string) file_get_contents($envPath);
$hasAppKey = (bool) preg_match('/^\s*APP_KEY\s*=\s*base64:/m', $envContents);

echo "=== CEK DATABASE ===\n";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo 'OK — '.env('DB_DATABASE')."\n\n";
} catch (Throwable $e) {
    http_response_code(500);
    exit("ERROR DB: ".$e->getMessage()."\n");
}

$commands = [];
if (! $hasAppKey) {
    $commands[] = ['key:generate', ['--force' => true]];
}
$commands[] = ['migrate', ['--force' => true]];
$commands[] = ['db:seed', ['--force' => true, '--class' => 'ConsultationVoucherSeeder']];
$commands[] = ['db:seed', ['--force' => true, '--class' => 'ConsultationProviderSeeder']];
$commands[] = ['db:seed', ['--force' => true, '--class' => 'MedicineSeeder']];
$commands[] = ['db:seed', ['--force' => true, '--class' => 'HomecarePackageSeeder']];
$commands[] = ['storage:link', ['--force' => true]];

echo "=== ARTISAN ===\n";
foreach ($commands as [$name, $options]) {
    echo ">>> php artisan {$name}\n";
    try {
        $status = $kernel->call($name, $options);
        $output = trim((string) $kernel->output());
        if ($output !== '') {
            echo $output."\n";
        }
        echo "Exit: {$status}\n\n";
        if ($status !== 0) {
            http_response_code(500);
            exit("GAGAL: {$name}\n");
        }
    } catch (Throwable $e) {
        http_response_code(500);
        exit("GAGAL: {$name}\n".$e->getMessage()."\n");
    }
}

echo "=== STORAGE SYMLINK (public_html) ===\n";
$storageTarget = $root.'/storage/app/public';
ensureStorageSymlink($root.'/public/storage', $storageTarget);
ensureStorageSymlink($root.'/public_html/storage', $storageTarget);
echo "\n";

echo "=== CEK KOLOM PENTING ===\n";
$checks = [
    'health_articles.content_type' => ['health_articles', 'content_type'],
    'health_articles.video_url' => ['health_articles', 'video_url'],
    'health_monitorings.medication_checks' => ['health_monitorings', 'medication_checks'],
    'consultation_orders.payment_proof' => ['consultation_orders', 'payment_proof'],
    'consultation_providers.photo' => ['consultation_providers', 'photo'],
    'consultation_messages.id' => ['consultation_messages', 'id'],
    'medicines.id' => ['medicines', 'id'],
    'medicine_orders.id' => ['medicine_orders', 'id'],
    'medicine_orders.shipping_receipt' => ['medicine_orders', 'shipping_receipt'],
    'homecare_packages.id' => ['homecare_packages', 'id'],
    'homecare_bookings.id' => ['homecare_bookings', 'id'],
];
foreach ($checks as $label => [$table, $column]) {
    $ok = \Illuminate\Support\Facades\Schema::hasTable($table)
        && \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
    echo ($ok ? 'OK' : 'MISSING')." — {$label}\n";
}
echo "\n";

$logFile = $root.'/storage/logs/laravel.log';
if (is_file($logFile)) {
    echo "=== LOG TERAKHIR (20 baris) ===\n";
    $lines = file($logFile, FILE_IGNORE_NEW_LINES) ?: [];
    echo implode("\n", array_slice($lines, -20))."\n\n";
}

echo "SELESAI.\n";
echo "1. Hapus setup-once.php dari public/ dan public_html/\n";
echo "2. Tes: https://chatbot-keperawatan.damgocompany.com/\n";
echo "3. Tes admin: https://chatbot-keperawatan.damgocompany.com/admin\n";
