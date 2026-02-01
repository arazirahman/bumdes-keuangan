<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AssetController;

Route::get('/', fn() => redirect()->route('dashboard'));

// AUTH
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =========================
    // TRANSAKSI
    // =========================
    // Lihat daftar transaksi (semua user login boleh)
    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transactions.index');

    // Operator & Superadmin: tambah + edit transaksi
    Route::middleware('role:operator,superadmin')->group(function () {
        Route::get('/transaksi/tambah', [TransactionController::class, 'create'])->name('transactions.create');
        Route::post('/transaksi', [TransactionController::class, 'store'])->name('transactions.store');

        Route::get('/transaksi/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
        Route::put('/transaksi/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    });

    // Superadmin: hapus transaksi
    Route::middleware('role:superadmin')->group(function () {
        Route::delete('/transaksi/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    });

    // =========================
    // ASET
    // =========================
    // Operator & Superadmin: lihat + tambah + edit aset
    Route::middleware('role:operator,superadmin')->group(function () {
        Route::get('/aset', [AssetController::class, 'index'])->name('assets.index');
        Route::get('/aset/tambah', [AssetController::class, 'create'])->name('assets.create');
        Route::post('/aset', [AssetController::class, 'store'])->name('assets.store');

        Route::get('/aset/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');
        Route::put('/aset/{asset}', [AssetController::class, 'update'])->name('assets.update');
    });

    // Superadmin: hapus aset
    Route::middleware('role:superadmin')->group(function () {
        Route::delete('/aset/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    });

    // =========================
    // LAPORAN (SUPERADMIN SAJA)
    // =========================
    Route::middleware(['auth', 'role:operator,superadmin'])->group(function () {
        Route::get('/laporan', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
        Route::get('/laporan/excel', [ReportController::class, 'excel'])->name('reports.excel');
    });
});
