<?php
/**
 * Import data obat & homecare dari file JSON ke hosting.
 * Upload file ini dan 'v21_data.json' ke public_html/ di hosting, lalu buka:
 * https://chatbot-keperawatan.damgocompany.com/import-v21-data.php?key=ck2026import
 * HAPUS file ini & v21_data.json setelah selesai!
 */

$key = 'ck2026import';
if (($_GET['key'] ?? '') !== $key) {
    http_response_code(404);
    exit('Not found');
}

define('LARAVEL_START', microtime(true));

$root = dirname(__DIR__);
require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';
/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

header('Content-Type: text/plain; charset=utf-8');

$jsonFile = __DIR__ . '/v21_data.json';

if (!is_file($jsonFile)) {
    http_response_code(404);
    exit("ERROR: File v21_data.json tidak ditemukan di " . __DIR__ . "\nUpload v21_data.json terlebih dahulu.");
}

$data = json_decode(file_get_contents($jsonFile), true);
if (!$data || !isset($data['medicines']) || !isset($data['homecare_packages'])) {
    http_response_code(400);
    exit("ERROR: Format file v21_data.json rusak atau tidak valid.");
}

echo "=== IMPORT DATA v21 ===\n";

try {
    DB::beginTransaction();

    // Matikan check foreign key sementara
    Schema::disableForeignKeyConstraints();

    // 1. Import Obat
    $medicines = $data['medicines'];
    DB::table('medicines')->truncate();
    foreach ($medicines as $item) {
        // Hapus ID jika auto increment, tapi kita ingin samakan ID dengan lokal agar relasi gambar/data tidak rusak
        DB::table('medicines')->insert($item);
    }
    echo "OK — Berhasil mengimpor " . count($medicines) . " data obat.\n";

    // 2. Import Homecare
    $homecare = $data['homecare_packages'];
    DB::table('homecare_packages')->truncate();
    foreach ($homecare as $item) {
        DB::table('homecare_packages')->insert($item);
    }
    echo "OK — Berhasil mengimpor " . count($homecare) . " data paket homecare.\n";

    Schema::enableForeignKeyConstraints();
    DB::commit();

    echo "\nIMPORT SELESAI Sempurna!\n";
    echo "Silakan HAPUS file 'import-v21-data.php' dan 'v21_data.json' dari server demi keamanan.\n";

} catch (Throwable $e) {
    DB::rollBack();
    Schema::enableForeignKeyConstraints();
    http_response_code(500);
    echo "ERROR IMPORT: " . $e->getMessage() . "\n";
}
