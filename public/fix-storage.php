<?php

declare(strict_types=1);

/**
 * Perbaiki folder storage → symlink ke storage/app/public
 * Upload ke public/ DAN public_html/, buka:
 * https://chatbot-keperawatan.damgocompany.com/fix-storage.php?key=ck2026fix
 * HAPUS setelah SELESAI.
 */
const FIX_KEY = 'ck2026fix';

if (($_GET['key'] ?? '') !== FIX_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');
set_time_limit(120);
ini_set('display_errors', '1');
error_reporting(E_ALL);

$root = dirname(__DIR__);
$target = $root.'/storage/app/public';

function ensureDir(string $path): void
{
    if (! is_dir($path)) {
        mkdir($path, 0775, true);
    }
}

function moveDirContents(string $from, string $to): int
{
    if (! is_dir($from)) {
        return 0;
    }

    ensureDir($to);
    $moved = 0;

    foreach (scandir($from) ?: [] as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $source = $from.'/'.$item;
        $dest = $to.'/'.$item;

        if (is_dir($source)) {
            $moved += moveDirContents($source, $dest);
            @rmdir($source);
            continue;
        }

        if (is_file($source)) {
            if (! is_file($dest)) {
                @rename($source, $dest);
            }
            $moved++;
        }
    }

    return $moved;
}

function removeStorageFolder(string $path): bool
{
    if (! is_dir($path) || is_link($path)) {
        return false;
    }

    foreach (scandir($path) ?: [] as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $full = $path.'/'.$item;

        if (is_dir($full)) {
            removeStorageFolder($full);
            continue;
        }

        @unlink($full);
    }

    return @rmdir($path);
}

function fixLink(string $linkPath, string $targetPath): void
{
    echo "=== {$linkPath} ===\n";
    $targetPath = rtrim($targetPath, '/');

    if (is_link($linkPath)) {
        echo "OK — sudah symlink → ".readlink($linkPath)."\n\n";

        return;
    }

    if (is_dir($linkPath)) {
        $moved = moveDirContents($linkPath, $targetPath);
        echo "Pindahkan {$moved} file ke storage/app/public\n";

        if (removeStorageFolder($linkPath)) {
            echo "Folder lama dihapus\n";
        } else {
            $remaining = array_diff(scandir($linkPath) ?: [], ['.', '..']);
            echo "PERINGATAN — folder masih berisi: ".implode(', ', $remaining)."\n";
            echo "Hapus manual folder ini lewat File Manager, lalu buka script ini lagi.\n\n";

            return;
        }
    } elseif (is_file($linkPath)) {
        unlink($linkPath);
    }

    ensureDir($targetPath);

    if (@symlink($targetPath, $linkPath)) {
        echo "OK — symlink dibuat\n\n";

        return;
    }

    echo "GAGAL symlink via PHP (hosting mungkin memblokir).\n";
    echo "Buat manual di File Manager DirectAdmin:\n";
    echo "  Link: storage\n";
    echo "  Target: ../storage/app/public\n";
    echo "  Lokasi: folder public/ DAN public_html/\n\n";
}

ensureDir($target);
ensureDir($target.'/article-videos');
ensureDir($target.'/article-covers');

echo "=== PERBAIKI STORAGE LINK ===\n\n";
fixLink($root.'/public/storage', $target);
fixLink($root.'/public_html/storage', $target);

echo "=== CEK AKSES ===\n";
echo 'public/storage: '.(is_link($root.'/public/storage') ? 'symlink OK' : (is_dir($root.'/public/storage') ? 'masih folder' : 'tidak ada'))."\n";
echo 'public_html/storage: '.(is_link($root.'/public_html/storage') ? 'symlink OK' : (is_dir($root.'/public_html/storage') ? 'masih folder' : 'tidak ada'))."\n";
echo 'storage/app/public: '.(is_dir($target) ? 'OK' : 'MISSING')."\n\n";

$logFile = $root.'/storage/logs/laravel.log';
if (is_file($logFile)) {
    $content = (string) file_get_contents($logFile);
    if (preg_match('/\[(\d{4}-\d{2}-\d{2}[^\]]+)\][^\n]*(?:ERROR|CRITICAL|Exception)[^\n]*\n(?:.*\n){0,5}/', $content, $m, PREG_OFFSET_CAPTURE)) {
        echo "=== ERROR TERAKHIR DI LOG ===\n";
        echo substr($content, $m[0][1], 800)."\n\n";
    }
}

echo "SELESAI — hapus fix-storage.php, lalu tes /admin dan upload video.\n";
