<?php

declare(strict_types=1);

/**
 * Perbaikan cepat error 500 Apache/Laravel setelah deploy.
 * Upload ke public_html/fix-500.php (document root)
 * Buka: https://chatbot-keperawatan.damgocompany.com/fix-500.php?key=ck2026fix
 * HAPUS setelah selesai.
 */
const FIX_KEY = 'ck2026fix';

if (($_GET['key'] ?? '') !== FIX_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$webRoot = __DIR__;
$root = dirname($webRoot);

echo "=== FIX ERROR 500 — chatbot-keperawatan ===\n\n";
echo 'PHP: '.PHP_VERSION."\n";
echo 'Web root: '.$webRoot."\n";
echo 'App root: '.$root."\n\n";

$actions = [];

function tryUnlink(string $path): bool
{
    return is_file($path) && @unlink($path);
}

function tryRename(string $from, string $to): bool
{
    if (! is_file($from)) {
        return false;
    }
    if (is_file($to)) {
        @unlink($to);
    }

    return @rename($from, $to);
}

echo "=== STRUKTUR FOLDER ===\n";
$structure = [
    'artisan' => $root.'/artisan',
    '.env' => $root.'/.env',
    'vendor/autoload.php' => $root.'/vendor/autoload.php',
    'bootstrap/app.php' => $root.'/bootstrap/app.php',
    'config/education.php' => $root.'/config/education.php',
    'public_html/index.php' => $webRoot.'/index.php',
    'storage (writable)' => $root.'/storage',
    'bootstrap/cache (writable)' => $root.'/bootstrap/cache',
];

foreach ($structure as $label => $path) {
    if (str_contains($label, 'writable')) {
        $ok = is_dir($path) && is_writable($path);
    } else {
        $ok = is_file($path);
    }
    echo ($ok ? 'OK' : 'MISSING')." — {$label}\n";
}

if (! is_file($root.'/artisan')) {
    echo "\nERROR KRITIS: artisan tidak di folder induk web root.\n";
    echo "ZIP harus di-extract di ROOT domain (sejajar public_html), BUKAN hanya isi public_html.\n";
    echo "Struktur benar:\n";
    echo "  /domains/chatbot-keperawatan.../artisan\n";
    echo "  /domains/chatbot-keperawatan.../public_html/index.php\n\n";
}

echo "\n=== PERBAIKAN OTOMATIS ===\n";

foreach (['hot'] as $bad) {
    foreach ([$webRoot.'/'.$bad, $root.'/public/'.$bad] as $hotPath) {
        if (is_file($hotPath) && @unlink($hotPath)) {
            $actions[] = 'Hapus '.$hotPath;
        }
    }
}

$cacheKeep = ['packages.php', 'services.php', '.gitignore'];
foreach (glob($root.'/bootstrap/cache/*.php') ?: [] as $file) {
    $base = basename($file);
    if (in_array($base, $cacheKeep, true)) {
        continue;
    }
    if (tryUnlink($file)) {
        $actions[] = 'Hapus bootstrap/cache/'.$base;
    }
}

foreach ([$webRoot.'/.user.ini', $root.'/public/.user.ini'] as $iniPath) {
    if (is_file($iniPath) && tryRename($iniPath, $iniPath.'.bak-'.date('Ymd'))) {
        $actions[] = 'Nonaktifkan sementara '.basename(dirname($iniPath)).'/.user.ini (rename .bak)';
    }
}

foreach ([$root.'/storage', $root.'/bootstrap/cache'] as $dir) {
    if (is_dir($dir)) {
        @chmod($dir, 0775);
    }
}

if ($actions === []) {
    echo "Tidak ada perubahan otomatis.\n";
} else {
    foreach ($actions as $action) {
        echo "- {$action}\n";
    }
}

echo "\n=== CEK .ENV ===\n";
if (! is_file($root.'/.env')) {
    echo "ERROR: .env HILANG — restore backup .env ke folder root (sejajar artisan).\n";
} else {
    $env = (string) file_get_contents($root.'/.env');
    echo 'APP_KEY: '.(preg_match('/^\s*APP_KEY\s*=\s*base64:.+/m', $env) ? 'OK' : 'KOSONG')."\n";
    echo 'APP_URL: '.(preg_match('/^\s*APP_URL\s*=\s*(.+)$/m', $env, $m) ? trim($m[1]) : '?')."\n";
    echo 'APP_DEBUG: '.(preg_match('/^\s*APP_DEBUG\s*=\s*true/mi', $env) ? 'true (sebaiknya false)' : 'false/OK')."\n";
}

echo "\n=== COBA BOOT LARAVEL ===\n";
try {
    require $root.'/vendor/autoload.php';
    $app = require_once $root.'/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    echo "OK — Laravel boot berhasil\n";
    echo 'APP_ENV: '.config('app.env')."\n";

    try {
        Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "OK — Database terhubung\n";
    } catch (Throwable $db) {
        echo 'ERROR DB: '.$db->getMessage()."\n";
    }
} catch (Throwable $e) {
    echo "ERROR BOOT:\n".$e->getMessage()."\n";
    echo $e->getFile().':'.$e->getLine()."\n";
}

echo "\n=== LANGKAH BERIKUTNYA ===\n";
echo "1. Refresh https://chatbot-keperawatan.damgocompany.com/\n";
echo "2. Jika sudah normal, buka clear-cache.php?key=ck2026fix\n";
echo "3. Jika masih 500 dan .user.ini di-backup, kembalikan nilai PHP lewat panel DirectAdmin (128M)\n";
echo "4. Hapus fix-500.php, check.php, clear-cache.php dari public_html\n";
echo "\nSELESAI.\n";
