<?php

declare(strict_types=1);

/**
 * Diagnostik server — tidak perlu Laravel.
 * Upload ke public/ dan public_html/, buka:
 * https://chatbot-keperawatan.damgocompany.com/check.php?key=ck2026fix
 * HAPUS setelah selesai.
 */
const CHECK_KEY = 'ck2026fix';

if (($_GET['key'] ?? '') !== CHECK_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$root = dirname(__DIR__);

echo "=== CHATBOT KEPERAWATAN — CEK SERVER ===\n\n";
echo 'PHP: '.PHP_VERSION."\n";
echo 'Root: '.$root."\n";
echo 'Waktu: '.date('c')."\n\n";

$paths = [
    '.env' => $root.'/.env',
    'artisan' => $root.'/artisan',
    'vendor/autoload.php' => $root.'/vendor/autoload.php',
    'bootstrap/app.php' => $root.'/bootstrap/app.php',
    'storage (writable)' => $root.'/storage',
    'bootstrap/cache (writable)' => $root.'/bootstrap/cache',
    'public/build/manifest.json' => $root.'/public/build/manifest.json',
    'public_html/index.php' => $root.'/public_html/index.php',
];

echo "=== FILE & FOLDER ===\n";
foreach ($paths as $label => $path) {
    if (str_contains($label, 'writable')) {
        $ok = is_dir($path) && is_writable($path);
    } else {
        $ok = is_file($path) || is_dir($path);
    }
    echo ($ok ? 'OK' : 'MISSING')." — {$label}\n";
}

echo "\n=== .ENV ===\n";
$envPath = $root.'/.env';
if (! is_file($envPath)) {
    echo "ERROR: .env tidak ada. Restore backup .env di folder root.\n";
} else {
    $env = (string) file_get_contents($envPath);
    echo 'APP_KEY: '.(preg_match('/^\s*APP_KEY\s*=\s*base64:/m', $env) ? 'OK' : 'KOSONG — jalankan setup-once')."\n";
    echo 'APP_DEBUG: '.(preg_match('/^\s*APP_DEBUG\s*=\s*true/mi', $env) ? 'true (matikan di production)' : 'false/OK')."\n";
    echo 'DB_DATABASE: '.(preg_match('/^\s*DB_DATABASE\s*=\s*(.+)$/m', $env, $m) ? trim($m[1]) : '?')."\n";
}

echo "\n=== CACHE BOOTSTRAP ===\n";
foreach (glob($root.'/bootstrap/cache/*.php') ?: [] as $file) {
    echo '- '.basename($file)."\n";
}

echo "\n=== COBA BOOT LARAVEL ===\n";
try {
    require $root.'/vendor/autoload.php';
    $app = require_once $root.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "OK — Laravel boot berhasil\n";
    echo 'APP_URL: '.config('app.url')."\n";
    echo 'APP_ENV: '.config('app.env')."\n";

    try {
        Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "OK — Database terhubung\n";
    } catch (Throwable $db) {
        echo "ERROR DB: ".$db->getMessage()."\n";
    }
} catch (Throwable $e) {
    echo "ERROR BOOT:\n".$e->getMessage()."\n\n";
    echo $e->getFile().':'.$e->getLine()."\n";
}

echo "\n=== SARAN JIKA ERROR 500 ===\n";
echo "1. Pastikan .env ada di root (sejajar artisan)\n";
echo "2. Hapus bootstrap/cache/config.php dan routes-v7.php (JANGAN hapus packages.php)\n";
echo "3. Permission storage & bootstrap/cache → 775\n";
echo "4. Jalankan setup-once.php?key=ck2026setup\n";
echo "5. Hapus public/hot dan public_html/hot\n";
echo "\nSELESAI — hapus check.php setelah dibaca.\n";
