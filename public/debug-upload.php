<?php

declare(strict_types=1);

const DEBUG_KEY = 'ck2026fix';

if (($_GET['key'] ?? '') !== DEBUG_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);

echo "=== CEK LIMIT UPLOAD PHP ===\n\n";
$keys = ['upload_max_filesize', 'post_max_size', 'max_input_time', 'max_execution_time', 'memory_limit', 'file_uploads'];
foreach ($keys as $key) {
    echo str_pad($key, 22).': '.ini_get($key)."\n";
}

require $root.'/vendor/autoload.php';
$app = require $root.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$appMaxKb = (int) config('education.video_max_upload_kb', 122880);
echo str_pad('app video max', 22).': '.round($appMaxKb / 1024, 1)." MB\n";

echo "\n=== CEK FOLDER UPLOAD ===\n";
$dirs = [
    'storage/app/public' => $root.'/storage/app/public',
    'storage/app/public/article-videos' => $root.'/storage/app/public/article-videos',
    'storage/app/public/article-covers' => $root.'/storage/app/public/article-covers',
];
foreach ($dirs as $label => $path) {
    $writable = is_dir($path) && is_writable($path);
    echo ($writable ? 'OK' : 'MISSING/LOCKED')." — {$label}\n";
}

echo "\n=== TES TULIS FILE ===\n";
$testFile = $root.'/storage/app/public/article-videos/.write-test-'.time().'.txt';
if (@file_put_contents($testFile, 'ok') !== false) {
    echo "OK — bisa menulis ke article-videos\n";
    @unlink($testFile);
} else {
    echo "GAGAL — tidak bisa menulis ke article-videos (permission)\n";
}

echo "\n=== SARAN ===\n";
$postMax = ini_get('post_max_size');
$uploadMax = ini_get('upload_max_filesize');
echo "- Batas aplikasi (Laravel): ".round($appMaxKb / 1024, 1)." MB\n";
echo "- Batas PHP upload: {$uploadMax} · post: {$postMax}\n";
echo "- post_max_size harus >= upload_max_filesize + ruang form/thumbnail\n";
echo "- Pastikan public/.user.ini ada di public_html (128M) — DirectAdmin/LiteSpeed\n";
echo "- php artisan serve (localhost) TIDAK baca .user.ini — set php.ini atau -d upload_max_filesize=128M\n";
echo "- Hosting: ModSecurity bisa blok POST besar meski PHP sudah 128M → gunakan Sudah di server / Link video\n";
echo "\nHapus debug-upload.php setelah dibaca.\n";
