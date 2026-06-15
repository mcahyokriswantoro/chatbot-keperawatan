<?php

use App\Http\Controllers\Admin\AdminAccessController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminMonitoringController;
use App\Http\Controllers\Admin\AdminScreeningController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DetectionController;
use App\Http\Controllers\EmergencyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HealthEducationController;
use App\Http\Controllers\HealthMonitoringController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProfilePageController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\ScreeningHistoryController;
use App\Http\Controllers\SelfManagementController;
use App\Http\Controllers\SelfManagementLogController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Route;

Route::redirect('/favicon.ico', '/favicon.png', 301);

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/edukasi', [HealthEducationController::class, 'index'])->name('education.index');
Route::get('/edukasi/{slug}', [HealthEducationController::class, 'show'])->name('education.show');

Route::get('/darurat', [EmergencyController::class, 'index'])->name('emergency');

Route::get('/profil', [ProfilePageController::class, 'index'])->name('profile.page');

Route::view('/bantuan', 'help.index')->name('help');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/deteksi/identitas', [DetectionController::class, 'identityForm'])->name('detection.identity');
    Route::post('/deteksi/identitas', [DetectionController::class, 'storeIdentity'])->name('detection.identity.store');
    Route::get('/deteksi', [DetectionController::class, 'index'])->name('detection.start');
    Route::get('/deteksi/{disease}/skrining', [DetectionController::class, 'chat'])->name('detection.chat.session');
    Route::get('/deteksi/{disease}', [DetectionController::class, 'show'])->name('detection.chat');
    Route::post('/api/screening', [ScreeningController::class, 'store'])->name('screening.store');
    Route::get('/api/wilayah/children', [WilayahController::class, 'children'])->name('wilayah.children');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/riwayat', [ScreeningHistoryController::class, 'index'])->name('history');
    Route::get('/riwayat/{id}', [ScreeningHistoryController::class, 'show'])->name('history.show');

    Route::get('/self-management', [SelfManagementController::class, 'index'])->name('self-management');
    Route::get('/self-management/{disease}', [SelfManagementController::class, 'show'])->name('self-management.show');

    Route::middleware('screening.completed')->group(function () {
        Route::post('/self-management/activities', [SelfManagementLogController::class, 'store'])->name('self-management.activities.store');
        Route::patch('/self-management/activities/{log}/toggle', [SelfManagementLogController::class, 'toggle'])->name('self-management.activities.toggle');

        Route::get('/monitoring', [HealthMonitoringController::class, 'index'])->name('monitoring');
        Route::post('/monitoring', [HealthMonitoringController::class, 'store'])->name('monitoring.store');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::get('/access', [AdminAccessController::class, 'index'])->name('access.index');
    Route::post('/access', [AdminAccessController::class, 'store'])->name('access.store');
    Route::delete('/access/{user}', [AdminAccessController::class, 'destroy'])->name('access.destroy');
    Route::get('/screenings', [AdminScreeningController::class, 'index'])->name('screenings.index');
    Route::get('/screenings/{screeningSession}', [AdminScreeningController::class, 'show'])->name('screenings.show');
    Route::get('/monitoring', [AdminMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{monitoring}', [AdminMonitoringController::class, 'show'])->name('monitoring.show');
    Route::get('/articles', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/articles/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/articles/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/articles/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/articles/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
});

require __DIR__.'/auth.php';
