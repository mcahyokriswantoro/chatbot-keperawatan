<?php

declare(strict_types=1);

/**
 * Setup sekali via browser (jika tidak ada Terminal cPanel).
 * 1. Salin file ini ke folder public/ di server
 * 2. Buka: https://chatbot-keperawatan.damgocompany.com/setup-once.php?key=ck2026setup
 * 3. HAPUS file public/setup-once.php setelah selesai
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

foreach ([
    $root.'/storage/framework/views',
    $root.'/storage/framework/cache/data',
    $root.'/storage/framework/sessions',
    $root.'/storage/logs',
    $root.'/bootstrap/cache',
] as $dir) {
    if (! is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}

require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$envPath = $root.'/.env';

if (! is_file($envPath)) {
    http_response_code(500);
    exit(
        "ERROR: File .env tidak ditemukan.\n\n".
        "Buat file .env di folder root project (sejajar dengan artisan/),\n".
        "salin isi dari deploy/env-production.example, lalu isi DB_DATABASE, DB_USERNAME, DB_PASSWORD.\n".
        "Pastikan ada baris: APP_KEY=\n"
    );
}

$envContents = (string) file_get_contents($envPath);
if (! preg_match('/^\s*APP_KEY\s*=/m', $envContents)) {
    $envContents = rtrim($envContents)."\nAPP_KEY=\n";
    file_put_contents($envPath, $envContents);
    echo ">>> Menambahkan baris APP_KEY= ke .env\n\n";
}

$hasAppKey = (bool) preg_match('/^\s*APP_KEY\s*=\s*base64:/m', $envContents);

echo ">>> Cek koneksi database...\n";
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "OK — terhubung ke: ".env('DB_DATABASE')."\n\n";
} catch (Throwable $e) {
    http_response_code(500);
    exit(
        "ERROR koneksi database:\n".$e->getMessage()."\n\n".
        "Periksa di .env:\n".
        "  DB_HOST=localhost\n".
        "  DB_DATABASE=nama_database (biasanya ada prefix akun, mis. damgocom_xxx)\n".
        "  DB_USERNAME=user_database (juga biasanya ada prefix)\n".
        "  DB_PASSWORD=...\n\n".
        "Di DirectAdmin: MySQL Management → pastikan user sudah di-assign ke database dengan ALL PRIVILEGES.\n"
    );
}

$commands = [];

if (! $hasAppKey) {
    $commands[] = ['key:generate', ['--force' => true]];
}

$commands = array_merge($commands, [
    ['migrate', ['--force' => true]],
    ['storage:link', ['--force' => true]],
    ['config:cache', []],
    ['route:cache', []],
    ['view:cache', []],
    ['ayosehat:sync', []],
]);

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
            exit("GAGAL pada perintah: {$name}. Perbaiki error di atas lalu refresh halaman ini.\n");
        }
    } catch (Throwable $e) {
        http_response_code(500);
        exit(
            "GAGAL pada perintah: {$name}\n\n".
            $e->getMessage()."\n\n".
            "Jika migrate: cek DB_DATABASE / DB_USERNAME / DB_PASSWORD di .env.\n".
            "Log detail: storage/logs/laravel.log\n"
        );
    }
}

echo "SELESAI. Segera HAPUS file public/setup-once.php!\n";
