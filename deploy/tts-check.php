<?php

declare(strict_types=1);

/**
 * Tes TTS neural via PHP (tanpa Node.js).
 * Upload ke public_html/tts-check.php
 * Buka: https://chatbot-keperawatan.damgocompany.com/tts-check.php?key=ck2026fix
 * HAPUS setelah selesai.
 */
const CHECK_KEY = 'ck2026fix';

if (($_GET['key'] ?? '') !== CHECK_KEY) {
    http_response_code(404);
    exit('Not found');
}

header('Content-Type: text/plain; charset=utf-8');
set_time_limit(120);

define('LARAVEL_START', microtime(true));

$root = dirname(__DIR__);

require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CEK TTS PHP v14 ===\n\n";
echo 'PHP: '.PHP_VERSION."\n";
echo 'Driver: '.config('screening_tts.driver', 'auto')."\n";
echo 'afaya/edge-tts: '.(class_exists(\Afaya\EdgeTTS\Service\EdgeTTS::class) ? 'OK' : 'TIDAK ADA')."\n";
echo 'curl: '.(extension_loaded('curl') ? 'OK' : 'TIDAK ADA')."\n";
echo 'openssl: '.(extension_loaded('openssl') ? 'OK' : 'TIDAK ADA')."\n\n";

try {
    /** @var \App\Services\ScreeningTtsService $tts */
    $tts = app(\App\Services\ScreeningTtsService::class);
    $bytes = $tts->synthesize('Halo, ini tes suara neural via PHP.', null);
    echo "SINTESIS: OK\n";
    echo 'Ukuran audio: '.strlen($bytes)." bytes\n";
    echo "Suara neural PHP siap dipakai.\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "SINTESIS: GAGAL\n";
    echo $e->getMessage()."\n";
}

echo "\nHapus tts-check.php setelah selesai.\n";
