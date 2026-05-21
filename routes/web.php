<?php

use App\Http\Controllers\DetectionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/deteksi', [DetectionController::class, 'index'])->name('detection.start');

Route::view('/profil', 'pages.placeholder', ['title' => 'Profil'])->name('profile.page');
Route::view('/riwayat', 'pages.placeholder', ['title' => 'Riwayat'])->name('history');
Route::view('/self-management', 'pages.placeholder', ['title' => 'Self Management'])->name('self-management');
Route::view('/monitoring', 'pages.placeholder', ['title' => 'Monitoring'])->name('monitoring');
Route::view('/bantuan', 'pages.placeholder', ['title' => 'Bantuan'])->name('help');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
