<?php
/**
 * Dump data medicines & homecare_packages dari database lokal.
 * Buka di browser lokal: http://localhost:8000/dump-v21-data.php
 * File JSON akan tersimpan di public/v21_data.json
 */

define('LARAVEL_START', microtime(true));

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';
/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

header('Content-Type: text/plain; charset=utf-8');

try {
    $medicines = DB::table('medicines')->get()->toArray();
    $homecare = DB::table('homecare_packages')->get()->toArray();

    $payload = [
        'medicines' => $medicines,
        'homecare_packages' => $homecare,
    ];

    $dest = __DIR__ . '/v21_data.json';
    file_put_contents($dest, json_encode($payload, JSON_PRETTY_PRINT));

    echo "DUMP BERHASIL!\n\n";
    echo "Detail Data:\n";
    echo "- Obat: " . count($medicines) . " data\n";
    echo "- Homecare: " . count($homecare) . " data\n\n";
    echo "File hasil dump disimpan di: public/v21_data.json\n";
    echo "Silakan upload file 'public/v21_data.json' tersebut ke folder 'public_html/' di server hosting kamu.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "ERROR DUMP: " . $e->getMessage() . "\n";
}
