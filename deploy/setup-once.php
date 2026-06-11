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

define('LARAVEL_START', microtime(true));

require dirname(__DIR__) . '/vendor/autoload.php';

$app = require_once dirname(__DIR__) . '/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$commands = [
    ['key:generate', ['--force' => true]],
    ['migrate', ['--force' => true]],
    ['storage:link', ['--force' => true]],
    ['config:cache', []],
    ['route:cache', []],
    ['view:cache', []],
    ['ayosehat:sync', []],
];

header('Content-Type: text/plain; charset=utf-8');

foreach ($commands as [$name, $options]) {
    echo ">>> php artisan {$name}\n";
    $status = $kernel->call($name, $options);
    echo trim((string) $kernel->output())."\n";
    echo "Exit: {$status}\n\n";
}

echo "SELESAI. Segera HAPUS file public/setup-once.php!\n";
