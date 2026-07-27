<?php
/**
 * Safe Debug file untuk melihat penyebab 403 Forbidden di halaman pembayaran.
 * Menggunakan Console Kernel dan nama class yang fully-qualified.
 */

header('Content-Type: text/plain; charset=utf-8');
ini_set('display_errors', '1');
error_reporting(E_ALL);

try {
    $root = dirname(__DIR__); // Standard path
    
    if (!is_file($root . '/vendor/autoload.php')) {
        $candidates = [__DIR__, dirname(dirname(__DIR__))];
        foreach ($candidates as $c) {
            if (is_file($c . '/vendor/autoload.php')) {
                $root = $c;
                break;
            }
        }
    }

    if (!is_file($root . '/vendor/autoload.php')) {
        throw new Exception("vendor/autoload.php tidak ditemukan!");
    }

    require $root . '/vendor/autoload.php';
    $app = require_once $root . '/bootstrap/app.php';

    // Gunakan Console Kernel (sama persis seperti setup-once.php)
    /** @var \Illuminate\Contracts\Console\Kernel $kernel */
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 3;

    echo "=== DB CONNECTION CHECK ===\n";
    try {
        \Illuminate\Support\Facades\DB::connection()->getPdo();
        echo "Database Connected: OK\n\n";
    } catch (Throwable $e) {
        throw new Exception("Koneksi Database Gagal: " . $e->getMessage());
    }

    echo "=== DEBUG ORDER ID: {$orderId} ===\n\n";

    if (\Illuminate\Support\Facades\Schema::hasTable('medicine_orders')) {
        $order = \Illuminate\Support\Facades\DB::table('medicine_orders')->where('id', $orderId)->first();

        if (!$order) {
            echo "ORDER DETAIL: Order ID {$orderId} TIDAK DITEMUKAN di database hosting!\n\n";
        } else {
            echo "ORDER DETAIL:\n";
            echo "- Order ID: " . $order->id . "\n";
            echo "- User ID Pemilik Order: " . $order->user_id . "\n";
            echo "- Code: " . $order->reference_code . "\n";
            echo "- Status: " . $order->status . "\n";
            echo "- Proof: " . ($order->payment_proof ? 'Ada' : 'Kosong') . "\n\n";
        }
    } else {
        echo "Tabel 'medicine_orders' tidak ditemukan!\n";
    }

    echo "=== DAFTAR USER DI DATABASE ===\n";
    if (\Illuminate\Support\Facades\Schema::hasTable('users')) {
        $users = \Illuminate\Support\Facades\DB::table('users')->orderBy('id')->get(['id', 'name', 'email']);
        foreach ($users as $u) {
            echo "- ID: {$u->id}, Nama: {$u->name}, Email: {$u->email}\n";
        }
    } else {
        echo "Tabel 'users' tidak ditemukan!\n";
    }

    echo "\n=== DAFTAR 10 ORDER TERAKHIR ===\n";
    if (\Illuminate\Support\Facades\Schema::hasTable('medicine_orders')) {
        $allOrders = \Illuminate\Support\Facades\DB::table('medicine_orders')->orderBy('id', 'desc')->limit(10)->get();
        if ($allOrders->isEmpty()) {
            echo "(Belum ada order obat di database)\n";
        } else {
            foreach ($allOrders as $ord) {
                echo "- ID Order: {$ord->id}, User ID: {$ord->user_id}, Code: {$ord->reference_code}, Status: {$ord->status}\n";
            }
        }
    }

} catch (Throwable $e) {
    echo "CRITICAL EXCEPTION:\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
}
