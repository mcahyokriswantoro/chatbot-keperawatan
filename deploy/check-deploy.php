<?php

declare(strict_types=1);

/**
 * Cek apakah update v14 sudah terpasang di server.
 * Upload ke public/check-deploy.php
 * Buka: https://chatbot-keperawatan.damgocompany.com/check-deploy.php?key=ck2026check
 * HAPUS file setelah selesai.
 */
const CHECK_KEY = 'ck2026check';

if (($_GET['key'] ?? '') !== CHECK_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);

echo "=== CEK DEPLOY v20 ===\n\n";

$versionFile = $root.'/public/version.txt';
$version = is_file($versionFile) ? trim((string) file_get_contents($versionFile)) : '(tidak ada)';
echo "Versi: {$version}\n";
echo 'Target: 2026-07-04-v20-full'.($version === '2026-07-04-v20-full' ? ' — OK' : ' — BELUM UPDATE')."\n\n";

$manifestPath = $root.'/public/build/manifest.json';
if (is_file($manifestPath)) {
    $manifest = json_decode((string) file_get_contents($manifestPath), true);
    $css = $manifest['resources/css/app.css']['file'] ?? '?';
    $js = $manifest['resources/js/app.js']['file'] ?? '?';
    echo "Build CSS: {$css}\n";
    echo "Build JS:  {$js}\n\n";
} else {
    echo "PERINGATAN: public/build/manifest.json tidak ada\n\n";
}

$checks = [
    'AdminConsultationProviderController.php' => $root.'/app/Http/Controllers/Admin/AdminConsultationProviderController.php',
    'providers/index.blade.php' => $root.'/resources/views/admin/consultations/providers/index.blade.php',
    'consultation_providers migration' => $root.'/database/migrations/2026_07_04_000002_create_consultation_providers_table.php',
    'ConsultationProvider.php' => $root.'/app/Models/ConsultationProvider.php',
    'activity-summary.blade.php' => $root.'/resources/views/admin/partials/activity-summary.blade.php',
];

echo "=== FILE PENTING ===\n";
foreach ($checks as $label => $path) {
    $ok = is_file($path);
    echo ($ok ? 'OK' : 'TIDAK ADA')." — {$label}\n";
}

echo "\n=== DATABASE ===\n";
if (is_file($root.'/vendor/autoload.php')) {
    require $root.'/vendor/autoload.php';
    $app = require $root.'/bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    $tables = [
        'consultation_providers',
        'consultation_orders',
        'consultation_vouchers',
    ];
    foreach ($tables as $table) {
        $ok = Illuminate\Support\Facades\Schema::hasTable($table);
        echo ($ok ? 'OK' : 'MISSING')." — tabel {$table}\n";
    }
} else {
    echo "SKIP — vendor tidak ada\n";
}

echo "\n=== LOKASI EXTRACT ===\n";
echo 'Root project (artisan): '.(is_file($root.'/artisan') ? 'OK' : 'TIDAK ADA')."\n";
echo 'Document root (index.php): '.(is_file($root.'/public/index.php') ? 'OK' : 'TIDAK ADA')."\n";

$wrongExtract = is_dir($root.'/public/app');
echo 'Salah extract ke public/app/: '.($wrongExtract ? 'YA — EXTRACT ULANG KE ROOT!' : 'tidak')."\n";

echo "\nJika BELUM UPDATE: extract ZIP di folder yang ada file artisan (bukan di dalam public/).\n";
echo "Setelah extract: setup-once.php lalu clear-cache.php\n";
