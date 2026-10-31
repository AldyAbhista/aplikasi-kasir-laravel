<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Route;

// 1. Pengarah Halaman Utama
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect('/admin');
        }

        return redirect('/kasir');
    }

    return redirect('/login');
});

// 2. Jalur Pengunjung (Guest) dengan Rate Limiting Ketat
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.post')->middleware('throttle:5,1');

    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'storeForgotPassword'])->name('forgot-password.post')->middleware('throttle:5,1');

    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'storeResetPassword'])->name('password.update')->middleware('throttle:5,1');
});

// 3. Jalur Berotentikasi (Wajib Login)
Route::middleware(['auth', 'prevent-back'])->group(function () {

    // Verifikasi Email
    Route::get('/email/verify', [AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])->middleware('throttle:6,1')->name('verification.send');

    // Logout & Ganti Password Mandiri
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/change-password', [AuthController::class, 'changePassword'])->name('change-password');
    Route::post('/change-password', [AuthController::class, 'storeChangePassword'])->name('change-password.post');

    // ============================================================
    // AREA KHUSUS KASIR (Admin & Kasir)
    // ============================================================
    Route::middleware(['verified', 'role:kasir,admin'])->group(function () {
        Route::get('/kasir-dashboard', function () {
            return view('kasir.dashboard');
        })->name('kasir.dashboard');

        Route::get('/kasir', [TransactionController::class, 'index'])->name('kasir.index');
        Route::post('/kasir/bayar', [TransactionController::class, 'store'])->name('kasir.store');
        Route::post('/kasir/qris/confirm', [TransactionController::class, 'confirmQris'])->name('kasir.qris.confirm');
        Route::post('/kasir/qris/cancel', [TransactionController::class, 'cancelQris'])->name('kasir.qris.cancel');
        Route::get('/kasir/struk/{id}', [TransactionController::class, 'struk'])->name('kasir.struk');
        Route::get('/kasir/history', [TransactionController::class, 'history'])->name('kasir.history');
    });

    // ============================================================
    // AREA KHUSUS ADMIN (Akses Penuh)
    // ============================================================
    Route::middleware(['verified', 'role:admin'])->group(function () {
        Route::get('/admin', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        // Pengaturan Toko & Audit Trail
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/activity-logs', [SettingController::class, 'activityLogs'])->name('admin.activity-logs');

        // Manajemen Pengguna
        Route::prefix('/users')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('users.index');
            Route::get('/tambah', [UserController::class, 'create'])->name('users.create');
            Route::post('/simpan', [UserController::class, 'store'])->name('users.store');
            Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
            Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
            Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
            Route::post('/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
            Route::get('/{id}/set-password', [UserController::class, 'setPassword'])->name('users.set-password');
            Route::post('/{id}/set-password', [UserController::class, 'storeSetPassword'])->name('users.set-password.store');
        });

        // Master Produk
        Route::prefix('/produk')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('products.index');
            Route::get('/tambah', [ProductController::class, 'create'])->name('products.create');
            Route::post('/simpan', [ProductController::class, 'store'])->name('products.store');
            Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
            Route::put('/{id}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        });

        // Laporan Keuangan
        Route::prefix('/laporan')->group(function () {
            Route::get('/', [ReportController::class, 'dashboard'])->name('laporan.index');
            Route::get('/bulanan', [ReportController::class, 'monthly'])->name('laporan.monthly');
            Route::get('/{id}/edit', [ReportController::class, 'edit'])->name('laporan.edit');
            Route::put('/{id}', [ReportController::class, 'update'])->name('laporan.update');
            Route::delete('/{id}', [ReportController::class, 'destroy'])->name('laporan.destroy');
            Route::get('/export/bulanan', [ReportController::class, 'export'])->name('laporan.export');
        });

        // Pembersihan Cache Aman via POST
        Route::post('/admin/clear-cache', function () {
            Artisan::call('optimize:clear');
            ActivityLog::record('clear_cache', 'Admin '.auth()->user()->name.' membersihkan cache aplikasi.');

            return back()->with('success', 'Cache aplikasi berhasil dibersihkan.');
        })->name('admin.clear-cache');
    });
});
