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

echo "=== DEBUG HTTP REQUEST ===\n\n";

$paths = [
    'public/admin' => $root.'/public/admin',
    'public_html/admin' => $root.'/public_html/admin',
    'public/articles' => $root.'/public/articles',
    'public_html/articles' => $root.'/public_html/articles',
];

echo "=== FOLDER KONFLIK (bisa bikin error 500) ===\n";
foreach ($paths as $label => $path) {
    if (is_dir($path)) {
        echo "MASALAH — {$label} ADA (hapus folder ini!)\n";
        foreach (glob($path.'/*') ?: [] as $item) {
            echo "  - ".basename($item)."\n";
        }
    } else {
        echo "OK — {$label} tidak ada\n";
    }
}
echo "\n";

require $root.'/vendor/autoload.php';
$app = require_once $root.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$routes = [
    '/admin/articles',
    '/admin/edukasi',
    '/admin',
    '/admin/monitoring',
];

echo "=== SIMULASI REQUEST (tanpa login) ===\n";
foreach ($routes as $uri) {
    try {
        $request = Illuminate\Http\Request::create($uri, 'GET');
        $response = $kernel->handle($request);
        echo $response->getStatusCode()." — {$uri}\n";
        $kernel->terminate($request, $response);
    } catch (Throwable $e) {
        echo "ERROR — {$uri}\n";
        echo $e->getMessage()."\n";
        echo $e->getFile().':'.$e->getLine()."\n\n";
    }
}

echo "\n=== ROUTE TERDAFTAR ===\n";
foreach (['admin.articles.index', 'admin.dashboard', 'admin.monitoring.index'] as $name) {
    try {
        echo route($name)."\n";
    } catch (Throwable $e) {
        echo "MISSING — {$name}\n";
    }
}

echo "\nHapus debug-http.php setelah dibaca.\n";
