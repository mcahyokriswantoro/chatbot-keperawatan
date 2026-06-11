<?php

declare(strict_types=1);

/**
 * Bersihkan cache + sync CSS ke public/css/site.css + public_html (jika ada).
 * Upload ke public/clear-cache.php
 * Buka: https://chatbot-keperawatan.damgocompany.com/clear-cache.php?key=ck2026fix
 * HAPUS file setelah selesai.
 */
const CLEAR_KEY = 'ck2026fix';

if (($_GET['key'] ?? '') !== CLEAR_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);

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
    $items = scandir($source) ?: [];

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $ok = copyPath($source.'/'.$item, $destination.'/'.$item) && $ok;
    }

    return $ok;
}

$removed = [];

foreach (glob($root.'/bootstrap/cache/*.php') ?: [] as $file) {
    if (basename($file) === '.gitignore') {
        continue;
    }
    if (@unlink($file)) {
        $removed[] = 'bootstrap/cache/'.basename($file);
    }
}

foreach (glob($root.'/storage/framework/views/*.php') ?: [] as $file) {
    if (basename($file) === '.gitignore') {
        continue;
    }
    if (@unlink($file)) {
        $removed[] = 'storage/framework/views/'.basename($file);
    }
}

echo "=== CLEAR CACHE ===\n";
if ($removed === []) {
    echo "Tidak ada file cache yang dihapus.\n\n";
} else {
    foreach ($removed as $path) {
        echo "- {$path}\n";
    }
    echo "\n";
}

echo "=== SYNC CSS ===\n";
$manifestFile = $root.'/public/build/manifest.json';
$siteCss = $root.'/public/css/site.css';

if (is_file($manifestFile)) {
    $manifest = json_decode((string) file_get_contents($manifestFile), true);
    $builtCss = $manifest['resources/css/app.css']['file'] ?? null;
    $builtCssPath = $builtCss ? $root.'/public/build/'.$builtCss : null;

    if ($builtCssPath && is_file($builtCssPath)) {
        if (! is_dir(dirname($siteCss))) {
            mkdir(dirname($siteCss), 0775, true);
        }
        copy($builtCssPath, $siteCss);
        echo "OK: public/css/site.css diperbarui dari {$builtCss}\n";
    } else {
        echo "PERINGATAN: file CSS build tidak ditemukan.\n";
    }
} else {
    echo "PERINGATAN: public/build/manifest.json tidak ada.\n";
}

$publicHtml = $root.'/public_html';
$syncFolders = ['build', 'css'];

if (is_dir($publicHtml)) {
    echo "\n=== SYNC public_html ===\n";
    foreach ($syncFolders as $folder) {
        $from = $root.'/public/'.$folder;
        $to = $publicHtml.'/'.$folder;
        if (is_dir($from)) {
            copyPath($from, $to);
            echo "OK: public/{$folder} -> public_html/{$folder}\n";
        }
    }
}

echo "\n=== CEK FILE ===\n";
$checks = [
    'public/build/manifest.json' => $root.'/public/build/manifest.json',
    'public/css/site.css' => $siteCss,
    'public/css/mobile-core.css' => $root.'/public/css/mobile-core.css',
    'public_html/css/site.css' => $publicHtml.'/css/site.css',
    'public_html/css/mobile-core.css' => $publicHtml.'/css/mobile-core.css',
    'public_html/build/manifest.json' => $publicHtml.'/build/manifest.json',
    'production-assets.blade.php' => $root.'/resources/views/components/app/production-assets.blade.php',
    'mobile.blade.php' => $root.'/resources/views/layouts/mobile.blade.php',
];

foreach ($checks as $label => $path) {
    echo $label.': '.(is_file($path) ? 'OK' : 'TIDAK ADA')."\n";
}

$mobileLayout = $root.'/resources/views/layouts/mobile.blade.php';
if (is_file($mobileLayout)) {
    $content = (string) file_get_contents($mobileLayout);
    echo 'mobile layout pakai production-assets: '.(str_contains($content, 'production-assets') ? 'OK' : 'BELUM UPDATE')."\n";
}

echo "\nTes buka CSS di browser:\n";
echo "https://chatbot-keperawatan.damgocompany.com/css/mobile-core.css\n";
echo "https://chatbot-keperawatan.damgocompany.com/css/site.css\n";

echo "\nSELESAI. Hapus public/clear-cache.php lalu refresh HP (incognito).\n";
