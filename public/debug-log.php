<?php

declare(strict_types=1);

const DEBUG_KEY = 'ck2026fix';

if (($_GET['key'] ?? '') !== DEBUG_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

$root = dirname(__DIR__);

echo "=== DEBUG LOG & CONFIG ===\n\n";

require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "APP_URL config: ".config('app.url')."\n";
echo "APP_ENV: ".config('app.env')."\n";
echo "APP_DEBUG: ".(config('app.debug') ? 'true' : 'false')."\n\n";

if (config('app.url') === 'http://localhost' || str_contains((string) config('app.url'), 'localhost')) {
    echo "MASALAH — APP_URL masih localhost!\n";
    echo "Hapus file: bootstrap/cache/config.php\n";
    echo "Pastikan .env: APP_URL=https://chatbot-keperawatan.damgocompany.com\n\n";
}

echo "=== LIMIT UPLOAD ===\n";
echo 'upload_max_filesize: '.ini_get('upload_max_filesize')."\n";
echo 'post_max_size: '.ini_get('post_max_size')."\n\n";

echo "=== CACHE BOOTSTRAP ===\n";
foreach (glob($root.'/bootstrap/cache/*.php') ?: [] as $file) {
    echo '- '.basename($file)."\n";
}
echo "\n";

$logFile = $root.'/storage/logs/laravel.log';
echo "=== LOG TERAKHIR (production) ===\n";
if (! is_file($logFile)) {
    echo "Tidak ada laravel.log\n";
} else {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES) ?: [];
    $production = array_values(array_filter($lines, function ($line) {
        return str_contains($line, 'chatbot-keperawatan')
            || str_contains($line, 'AdminArticle')
            || str_contains($line, 'production.ERROR')
            || str_contains($line, 'damgocom');
    }));
    $tail = array_slice($production !== [] ? $production : $lines, -25);
    echo implode("\n", $tail)."\n";
}

echo "\n=== TES SIMPAN ARTIKEL (tanpa file) ===\n";
try {
    $slug = 'test-'.time();
    $article = App\Models\HealthArticle::create([
        'title' => 'Test Debug',
        'slug' => $slug,
        'category' => 'Test',
        'content_type' => 'article',
        'excerpt' => 'test',
        'content' => 'test',
        'is_published' => false,
    ]);
    echo "OK — insert DB id={$article->id}\n";
    $article->delete();
    echo "OK — test record dihapus\n";
} catch (Throwable $e) {
    echo "ERROR DB: ".$e->getMessage()."\n";
}

echo "\nHapus debug-log.php setelah dibaca.\n";
