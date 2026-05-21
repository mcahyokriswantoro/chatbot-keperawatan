<?php

use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetectionController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\HealthEducationController;
use App\Http\Controllers\HealthMonitoringController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePageController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\ScreeningHistoryController;
use App\Http\Controllers\SelfManagementController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/edukasi', [HealthEducationController::class, 'index'])->name('education.index');
Route::get('/edukasi/{slug}', [HealthEducationController::class, 'show'])->name('education.show');

Route::get('/darurat', [EmergencyController::class, 'index'])->name('emergency');

Route::get('/profil', [ProfilePageController::class, 'index'])->name('profile.page');

Route::view('/bantuan', 'help.index')->name('help');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/deteksi', [DetectionController::class, 'index'])->name('detection.start');
    Route::get('/deteksi/{disease}/skrining', [DetectionController::class, 'chat'])->name('detection.chat.session');
    Route::get('/deteksi/{disease}', [DetectionController::class, 'show'])->name('detection.chat');
    Route::post('/deteksi/{disease}/identitas', [DetectionController::class, 'storeIdentity'])->name('detection.identity.store');
    Route::post('/api/screening', [ScreeningController::class, 'store'])->name('screening.store');
    Route::get('/api/wilayah/children', [WilayahController::class, 'children'])->name('wilayah.children');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/riwayat', [ScreeningHistoryController::class, 'index'])->name('history');
    Route::get('/riwayat/{id}', [ScreeningHistoryController::class, 'show'])->name('history.show');

    Route::get('/self-management', [SelfManagementController::class, 'index'])->name('self-management');
    Route::post('/self-management', [SelfManagementController::class, 'store'])->name('self-management.store');
    Route::patch('/self-management/{log}/toggle', [SelfManagementController::class, 'toggle'])->name('self-management.toggle');

    Route::get('/monitoring', [HealthMonitoringController::class, 'index'])->name('monitoring');
    Route::post('/monitoring', [HealthMonitoringController::class, 'store'])->name('monitoring.store');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
});

require __DIR__.'/auth.php';
