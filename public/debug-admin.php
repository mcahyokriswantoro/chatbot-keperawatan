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
require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUG ADMIN ARTICLES ===\n\n";

$checks = [
    'HealthArticle model' => is_file($root.'/app/Models/HealthArticle.php'),
    'AdminArticleController' => is_file($root.'/app/Http/Controllers/Admin/AdminArticleController.php'),
    'admin/articles/index view' => is_file($root.'/resources/views/admin/articles/index.blade.php'),
    'admin/articles/form view' => is_file($root.'/resources/views/admin/articles/form.blade.php'),
    'admin-video-thumbnail.js' => is_file($root.'/public/build/manifest.json'),
];

foreach ($checks as $label => $ok) {
    echo ($ok ? 'OK' : 'MISSING')." — {$label}\n";
}

echo "\n=== MODEL METHODS ===\n";
$methods = ['isVideo', 'isArticle', 'contentTypeLabel', 'coverImageUrl', 'videoPlaybackUrl'];
foreach ($methods as $method) {
    echo (method_exists(App\Models\HealthArticle::class, $method) ? 'OK' : 'MISSING')." — {$method}()\n";
}

echo "\n=== QUERY ARTIKEL ===\n";
try {
    $articles = App\Models\HealthArticle::latest()->paginate(15);
    echo 'OK — '.$articles->total()." artikel\n";
    foreach ($articles as $article) {
        echo "- #{$article->id} {$article->title} | type=".($article->content_type ?? 'null');
        try {
            echo ' | cover='.($article->coverImageUrl() ? 'yes' : 'no');
            echo ' | isVideo='.($article->isVideo() ? 'yes' : 'no');
        } catch (Throwable $e) {
            echo ' | ERROR: '.$e->getMessage();
        }
        echo "\n";
    }
} catch (Throwable $e) {
    echo "ERROR query: ".$e->getMessage()."\n";
}

echo "\n=== RENDER VIEW ===\n";
try {
    $articles = App\Models\HealthArticle::latest()->paginate(15);
    $html = view('admin.articles.index', compact('articles'))->render();
    echo 'OK — view rendered ('.strlen($html)." bytes)\n";
} catch (Throwable $e) {
    echo "ERROR view:\n".$e->getMessage()."\n";
    echo $e->getFile().':'.$e->getLine()."\n";
}

echo "\nHapus debug-admin.php setelah dibaca.\n";
