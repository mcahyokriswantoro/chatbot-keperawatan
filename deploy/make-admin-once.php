<?php

declare(strict_types=1);

/**
 * Jadikan user admin via browser (sekali pakai).
 * 1. Copy ke public/make-admin-once.php
 * 2. Buka: https://chatbot-keperawatan.damgocompany.com/make-admin-once.php?key=ck2026admin&email=EMAIL_ANDA
 * 3. HAPUS file setelah selesai
 */
const ADMIN_KEY = 'ck2026admin';

if (($_GET['key'] ?? '') !== ADMIN_KEY) {
    http_response_code(404);
    exit('Not found');
}

$email = trim((string) ($_GET['email'] ?? ''));

if ($email === '') {
    header('Content-Type: text/plain; charset=utf-8');
    exit("ERROR: tambahkan email di URL.\nContoh: ?key=ck2026admin&email=cahyo.krizt@gmail.com\n");
}

define('LARAVEL_START', microtime(true));

require dirname(__DIR__) . '/vendor/autoload.php';

$app = require_once dirname(__DIR__) . '/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

try {
    $user = app(\App\Services\AdminAccessService::class)->grantByEmail($email);
    echo "SELESAI\n\n";
    echo "Nama: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "is_admin: ".($user->isAdmin() ? 'ya' : 'tidak')."\n";
    echo "email_verified_at: {$user->email_verified_at}\n\n";
    echo "Login di /login lalu buka /admin\n";
    echo "HAPUS file public/make-admin-once.php sekarang!\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo 'GAGAL: '.$e->getMessage()."\n";
}
