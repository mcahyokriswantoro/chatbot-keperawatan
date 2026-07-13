<?php

use App\Http\Controllers\Admin\AdminAccessController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminConsultationChatController;
use App\Http\Controllers\Admin\AdminConsultationProviderController;
use App\Http\Controllers\Admin\AdminConsultationVoucherController;
use App\Http\Controllers\Admin\AdminConsultationController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminMonitoringController;
use App\Http\Controllers\Admin\AdminScreeningController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\ConsultationController;
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
use App\Http\Controllers\ScreeningTtsController;
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

Route::get('/konsultasi', [ConsultationController::class, 'index'])->name('consultation.index');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/konsultasi/{provider}/checkout', [ConsultationController::class, 'checkout'])->name('consultation.checkout');
    Route::get('/konsultasi/{provider}/pembayaran', [ConsultationController::class, 'payment'])->name('consultation.payment');
    Route::get('/konsultasi/{provider}/pembayaran/status', [ConsultationController::class, 'paymentStatus'])->name('consultation.payment.status');
    Route::get('/konsultasi/{provider}/pembayaran/poll', [ConsultationController::class, 'paymentPoll'])->name('consultation.payment.poll');
    Route::post('/konsultasi/{provider}/voucher', [ConsultationController::class, 'redeemVoucher'])->name('consultation.voucher');
    Route::post('/konsultasi/{provider}/pembayaran/dana', [ConsultationController::class, 'payDana'])->name('consultation.pay.dana');
    Route::post('/konsultasi/{provider}/pay', [ConsultationController::class, 'pay'])->name('consultation.pay');
    Route::get('/konsultasi/{provider}/chat', [ConsultationController::class, 'chat'])->name('consultation.chat');
    Route::get('/konsultasi/{provider}/chat/pesan', [ConsultationController::class, 'messages'])->name('consultation.chat.messages');
    Route::post('/konsultasi/{provider}/chat', [ConsultationController::class, 'send'])->name('consultation.send');
    Route::get('/konsultasi/{category}', [ConsultationController::class, 'category'])->name('consultation.category');
});

Route::view('/bantuan', 'help.index')->name('help');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/deteksi/identitas', [DetectionController::class, 'identityForm'])->name('detection.identity');
    Route::post('/deteksi/identitas', [DetectionController::class, 'storeIdentity'])->name('detection.identity.store');
    Route::get('/deteksi', [DetectionController::class, 'index'])->name('detection.start');
    Route::get('/deteksi/pilih-skrining', [DetectionController::class, 'menu'])->name('detection.menu');
    Route::get('/deteksi/skrining-awal', [DetectionController::class, 'initialScreening'])->name('detection.initial');
    Route::get('/deteksi/{disease}/skrining', [DetectionController::class, 'chat'])->name('detection.chat.session');
    Route::get('/deteksi/{disease}', [DetectionController::class, 'show'])->name('detection.chat');
    Route::post('/api/screening', [ScreeningController::class, 'store'])->name('screening.store');
    Route::post('/api/screening-tts', [ScreeningTtsController::class, 'store'])->name('screening.tts');
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
        Route::get('/monitoring/preview', [HealthMonitoringController::class, 'preview'])->name('monitoring.preview');
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
    Route::post('/access/provider', [AdminAccessController::class, 'storeProvider'])->name('access.store-provider');
    Route::delete('/access/provider/{user}', [AdminAccessController::class, 'destroyProvider'])->name('access.destroy-provider');

    Route::get('/screenings', [AdminScreeningController::class, 'index'])->name('screenings.index');
    Route::get('/screenings/{screeningSession}', [AdminScreeningController::class, 'show'])->name('screenings.show');
    Route::get('/monitoring', [AdminMonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{monitoring}', [AdminMonitoringController::class, 'show'])->name('monitoring.show');
    Route::get('/edukasi', [AdminArticleController::class, 'index'])->name('articles.index');
    Route::get('/edukasi/create', [AdminArticleController::class, 'create'])->name('articles.create');
    Route::post('/edukasi', [AdminArticleController::class, 'store'])->name('articles.store');
    Route::get('/edukasi/{article}/edit', [AdminArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/edukasi/{article}', [AdminArticleController::class, 'update'])->name('articles.update');
    Route::delete('/edukasi/{article}', [AdminArticleController::class, 'destroy'])->name('articles.destroy');
    Route::get('/konsultasi/chat', [AdminConsultationChatController::class, 'index'])->name('consultations.chat.index');
    Route::get('/konsultasi/chat/{order}', [AdminConsultationChatController::class, 'show'])->name('consultations.chat.show');
    Route::get('/konsultasi/chat/{order}/pesan', [AdminConsultationChatController::class, 'messages'])->name('consultations.chat.messages');
    Route::post('/konsultasi/chat/{order}/balas', [AdminConsultationChatController::class, 'reply'])->name('consultations.chat.reply');
    Route::get('/konsultasi/tenaga-kesehatan', [AdminConsultationProviderController::class, 'index'])->name('consultations.providers.index');
    Route::get('/konsultasi/tenaga-kesehatan/tambah', [AdminConsultationProviderController::class, 'create'])->name('consultations.providers.create');
    Route::post('/konsultasi/tenaga-kesehatan', [AdminConsultationProviderController::class, 'store'])->name('consultations.providers.store');
    Route::get('/konsultasi/tenaga-kesehatan/{provider}/edit', [AdminConsultationProviderController::class, 'edit'])->name('consultations.providers.edit');
    Route::match(['put', 'post'], '/konsultasi/tenaga-kesehatan/{provider}', [AdminConsultationProviderController::class, 'update'])->name('consultations.providers.update');
    Route::post('/konsultasi/tenaga-kesehatan/{provider}/toggle', [AdminConsultationProviderController::class, 'toggle'])->name('consultations.providers.toggle');
    Route::delete('/konsultasi/tenaga-kesehatan/{provider}', [AdminConsultationProviderController::class, 'destroy'])->name('consultations.providers.destroy');
    Route::get('/konsultasi/voucher', [AdminConsultationVoucherController::class, 'index'])->name('consultations.vouchers.index');
    Route::post('/konsultasi/voucher', [AdminConsultationVoucherController::class, 'store'])->name('consultations.vouchers.store');
    Route::put('/konsultasi/voucher/{voucher}', [AdminConsultationVoucherController::class, 'update'])->name('consultations.vouchers.update');
    Route::post('/konsultasi/voucher/{voucher}/toggle', [AdminConsultationVoucherController::class, 'toggle'])->name('consultations.vouchers.toggle');
    Route::delete('/konsultasi/voucher/{voucher}', [AdminConsultationVoucherController::class, 'destroy'])->name('consultations.vouchers.destroy');
    Route::get('/konsultasi', [AdminConsultationController::class, 'index'])->name('consultations.index');
    Route::post('/konsultasi/{order}/setujui', [AdminConsultationController::class, 'approve'])->name('consultations.approve');
    Route::post('/konsultasi/{order}/tolak', [AdminConsultationController::class, 'reject'])->name('consultations.reject');
});

require __DIR__.'/auth.php';
