<?php

declare(strict_types=1);

/**
 * Perbaikan deploy DirectAdmin: sync public/ → public_html/ + bersihkan cache.
 *
 * CARA PAKAI (pilih salah satu):
 * A) Upload ke public_html/fix-deploy.php lalu buka:
 *    https://chatbot-keperawatan.damgocompany.com/fix-deploy.php?key=ck2026fix
 * B) Upload ke public/fix-deploy.php jika document root = public/
 *
 * HAPUS file setelah muncul SELESAI.
 */
const FIX_KEY = 'ck2026fix';

if (($_GET['key'] ?? '') !== FIX_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');
set_time_limit(300);

function copyPath(string $source, string $destination): bool
{
    if (is_file($source)) {
        $dir = dirname($destination);
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        return copy($source, $destination);
    }

    if (! is_dir($source)) {
        return false;
    }

    if (! is_dir($destination) && ! mkdir($destination, 0775, true) && ! is_dir($destination)) {
        return false;
    }

    $ok = true;
    foreach (scandir($source) ?: [] as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $ok = copyPath($source.'/'.$item, $destination.'/'.$item) && $ok;
    }

    return $ok;
}

function readManifestCss(string $path): string
{
    if (! is_file($path)) {
        return '(tidak ada)';
    }
    $manifest = json_decode((string) file_get_contents($path), true);

    return (string) ($manifest['resources/css/app.css']['file'] ?? '?');
}

$here = __DIR__;
$root = dirname($here);
$runningIn = basename($here);

// Laravel root = folder yang ada artisan
if (! is_file($root.'/artisan')) {
    http_response_code(500);
    exit(
        "ERROR: Folder Laravel tidak ditemukan.\n".
        "Script ini harus di public/ atau public_html/ (sejajar dengan app/, vendor/).\n".
        "Saat ini: {$here}\n".
        "Parent: {$root}\n"
    );
}

$publicDir = $root.'/public';
$publicHtml = $root.'/public_html';
$webRoot = is_dir($publicHtml) ? $publicHtml : $publicDir;

echo "=== FIX DEPLOY v14 ===\n\n";
echo "Laravel root: {$root}\n";
echo "Script di: {$runningIn}/\n";
echo "Web root aktif: ".basename($webRoot)."/\n\n";

echo "=== CEK VERSI (sebelum sync) ===\n";
$versionPublic = is_file($publicDir.'/version.txt') ? trim((string) file_get_contents($publicDir.'/version.txt')) : '(tidak ada)';
$versionWeb = is_file($webRoot.'/version.txt') ? trim((string) file_get_contents($webRoot.'/version.txt')) : '(tidak ada)';
echo "public/version.txt: {$versionPublic}\n";
echo basename($webRoot)."/version.txt: {$versionWeb}\n";
echo 'activity-summary.blade.php: '.(is_file($root.'/resources/views/admin/partials/activity-summary.blade.php') ? 'OK' : 'TIDAK ADA')."\n";
echo 'MonitoringDerivedFieldsSync: '.(is_file($root.'/app/Services/MonitoringDerivedFieldsSync.php') ? 'OK' : 'TIDAK ADA')."\n\n";

if ($webRoot !== $publicDir && is_dir($publicDir)) {
    echo "=== SYNC public/ → ".basename($webRoot)."/ (SEMUA file) ===\n";
    $syncOk = copyPath($publicDir, $webRoot);
    echo ($syncOk ? 'OK' : 'GAGAL sebagian')." — public/ disalin ke ".basename($webRoot)."/\n\n";
} elseif ($webRoot === $publicDir) {
    echo "=== SYNC ===\n";
    echo "Document root = public/ — tidak perlu sync ke public_html.\n\n";
} else {
    echo "PERINGATAN: folder public/ tidak ada.\n\n";
}

echo "=== BUILD CSS (setelah sync) ===\n";
echo 'public/build: '.readManifestCss($publicDir.'/build/manifest.json')."\n";
echo basename($webRoot).'/build: '.readManifestCss($webRoot.'/build/manifest.json')."\n";
echo 'Target v14: app-1AYJwV7I.css'."\n\n";

echo "=== CLEAR CACHE ===\n";
foreach (glob($root.'/bootstrap/cache/*.php') ?: [] as $file) {
    if (basename($file) !== '.gitignore') {
        @unlink($file);
        echo '- bootstrap/cache/'.basename($file)."\n";
    }
}
foreach (glob($root.'/storage/framework/views/*.php') ?: [] as $file) {
    if (basename($file) !== '.gitignore') {
        @unlink($file);
    }
}
echo "View cache dihapus.\n\n";

if (is_file($root.'/vendor/autoload.php')) {
    define('LARAVEL_START', microtime(true));
    require $root.'/vendor/autoload.php';
    $app = require_once $root.'/bootstrap/app.php';
    /** @var \Illuminate\Contracts\Console\Kernel $kernel */
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    foreach (['config:clear', 'route:clear', 'view:clear', 'cache:clear'] as $cmd) {
        echo ">>> php artisan {$cmd}\n";
        try {
            $kernel->call($cmd);
            echo trim((string) $kernel->output())."\n\n";
        } catch (Throwable $e) {
            echo 'ERROR: '.$e->getMessage()."\n\n";
        }
    }
}

echo "=== CEK AKHIR ===\n";
echo basename($webRoot).'/version.txt: ';
echo is_file($webRoot.'/version.txt') ? trim((string) file_get_contents($webRoot.'/version.txt')) : '(tidak ada)';
echo "\n";
echo basename($webRoot).'/build CSS: '.readManifestCss($webRoot.'/build/manifest.json')."\n\n";

echo "Buka di browser (harus 2026-06-24-v14 + app-1AYJwV7I.css):\n";
echo "https://chatbot-keperawatan.damgocompany.com/version.txt\n";
echo "https://chatbot-keperawatan.damgocompany.com/build/manifest.json\n\n";

echo "SELESAI. Hapus {$runningIn}/fix-deploy.php lalu hard refresh /admin.\n";
