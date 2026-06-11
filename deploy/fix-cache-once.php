<?php

declare(strict_types=1);

/**
 * Bersihkan cache Laravel via browser (jika situs error 500 setelah setup).
 * 1. Salin ke public/fix-cache-once.php
 * 2. Buka: https://chatbot-keperawatan.damgocompany.com/fix-cache-once.php?key=ck2026fix
 * 3. HAPUS file setelah selesai
 */
const FIX_KEY = 'ck2026fix';

if (($_GET['key'] ?? '') !== FIX_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');
set_time_limit(120);

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

$commands = [
    'config:clear',
    'route:clear',
    'view:clear',
    'cache:clear',
];

foreach ($commands as $name) {
    echo ">>> php artisan {$name}\n";
    try {
        $status = $kernel->call($name);
        $output = trim((string) $kernel->output());
        if ($output !== '') {
            echo $output."\n";
        }
        echo "Exit: {$status}\n\n";
    } catch (Throwable $e) {
        echo "ERROR: ".$e->getMessage()."\n\n";
    }
}

$manifest = dirname(__DIR__) . '/public/build/manifest.json';
echo $manifest && is_file($manifest)
    ? "OK: public/build/manifest.json ada\n"
    : "PERINGATAN: public/build/manifest.json TIDAK ADA — upload folder public/build dari laptop\n";

echo "\nSELESAI. Hapus public/fix-cache-once.php lalu refresh situs.\n";
