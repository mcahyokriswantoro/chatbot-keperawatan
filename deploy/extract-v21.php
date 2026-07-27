<?php
/**
 * Extract ZIP v21 otomatis ke folder root project.
 * Upload file ini + ZIP ke root project, lalu buka:
 * https://chatbot-keperawatan.damgocompany.com/../extract-v21.php?key=ck2026extract
 * HAPUS file ini setelah selesai!
 */

$key = 'ck2026extract';
if (($_GET['key'] ?? '') !== $key) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');
set_time_limit(600);
ini_set('display_errors', '1');
error_reporting(E_ALL);

$root = __DIR__;
// If this script is inside public/ or public_html/, go up one level
if (basename($root) === 'public' || basename($root) === 'public_html') {
    $root = dirname($root);
}

echo "=== EXTRACT ZIP v21 ===\n";
echo "Root: {$root}\n\n";

// Find the ZIP file
$zipName = 'chatbot-keperawatan-full-15juli2026-v21.zip';
$zipPath = null;

$candidates = [
    $root . '/' . $zipName,
    $root . '/public/' . $zipName,
    $root . '/public_html/' . $zipName,
    __DIR__ . '/' . $zipName,
];

foreach ($candidates as $c) {
    if (is_file($c)) {
        $zipPath = $c;
        break;
    }
}

if (!$zipPath) {
    echo "ERROR: ZIP tidak ditemukan!\n";
    echo "Upload file {$zipName} ke salah satu lokasi:\n";
    foreach ($candidates as $c) {
        echo "  - {$c}\n";
    }
    exit;
}

echo "ZIP ditemukan: {$zipPath}\n";
echo "Size: " . round(filesize($zipPath) / 1024 / 1024, 1) . " MB\n\n";

$zip = new ZipArchive();
$result = $zip->open($zipPath);

if ($result !== true) {
    echo "ERROR: Tidak bisa buka ZIP (code: {$result})\n";
    exit;
}

$total = $zip->numFiles;
echo "Total file dalam ZIP: {$total}\n";
echo "Extracting ke: {$root}\n\n";

// Skip files that should not be extracted
$skip = ['.env', 'public/.user.ini', 'public\\/.user.ini', 'public/hot', 'public\\/hot'];

$extracted = 0;
$skipped = 0;
$errors = 0;

for ($i = 0; $i < $total; $i++) {
    $name = $zip->getNameIndex($i);
    
    // Skip dangerous files
    $shouldSkip = false;
    foreach ($skip as $s) {
        if ($name === $s || $name === str_replace('/', '\\', $s)) {
            $shouldSkip = true;
            break;
        }
    }
    
    if ($shouldSkip) {
        echo "SKIP: {$name}\n";
        $skipped++;
        continue;
    }
    
    $extracted++;
}

// Extract all at once to root
echo "Extracting {$extracted} files...\n";

if ($zip->extractTo($root)) {
    echo "OK — Extract selesai!\n\n";
} else {
    echo "ERROR — Gagal extract!\n";
    $zip->close();
    exit;
}

$zip->close();

// Sync files from public to public_html if public_html exists (DirectAdmin default)
if (is_dir($root . '/public_html')) {
    echo "=== SYNC PUBLIC -> PUBLIC_HTML ===\n";
    echo "Menyalin file dari public/ ke public_html/...\n";
    
    if (!function_exists('copyFolder')) {
        function copyFolder($src, $dst) {
            if (!is_dir($src)) return;
            if (!is_dir($dst)) {
                mkdir($dst, 0755, true);
            }
            $dir = opendir($src);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        copyFolder($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }
    }
    
    copyFolder($root . '/public', $root . '/public_html');
    echo "OK — Sinkronisasi public_html selesai!\n\n";
}

// Remove dangerous files that might have been extracted
$removeFiles = [
    $root . '/public/.user.ini',
    $root . '/public/hot',
    $root . '/public_html/hot',
];

echo "=== HAPUS FILE BERBAHAYA ===\n";
foreach ($removeFiles as $f) {
    if (file_exists($f)) {
        if (unlink($f)) {
            echo "HAPUS: {$f}\n";
        } else {
            echo "GAGAL HAPUS: {$f} — hapus manual!\n";
        }
    }
}

// Check version
echo "\n=== VERIFIKASI ===\n";
$versionFile = $root . '/public/version.txt';
if (is_file($versionFile)) {
    echo "version.txt: " . trim(file_get_contents($versionFile)) . "\n";
} else {
    $versionFile2 = $root . '/public_html/version.txt';
    if (is_file($versionFile2)) {
        echo "version.txt: " . trim(file_get_contents($versionFile2)) . "\n";
    } else {
        echo "version.txt: TIDAK DITEMUKAN\n";
    }
}

// Check key files exist
$checks = ['artisan', 'routes/web.php', 'app/Http/Controllers/MedicineController.php', 'app/Http/Controllers/HomecareController.php'];
foreach ($checks as $c) {
    $exists = is_file($root . '/' . $c) ? 'OK' : 'MISSING';
    echo "{$c}: {$exists}\n";
}

echo "\n=== SELESAI ===\n";
echo "1. HAPUS file ini (extract-v21.php) dari server\n";
echo "2. HAPUS file ZIP dari server\n";
echo "3. Jalankan setup-once.php (migration)\n";
echo "4. Jalankan clear-cache.php\n";
