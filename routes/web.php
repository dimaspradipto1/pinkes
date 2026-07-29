<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FotoBarangController;
use App\Http\Controllers\FotoRuanganController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PinjamController;
use App\Http\Controllers\RekapitulasiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontController::class, 'index'])->name('index');

Route::controller(AuthController::class)->group(function () {
    Route::get('/login',       'login')->name('login');
    Route::post('/loginproses','loginproses')->name('loginproses');
    Route::get('/logout',      'logout')->name('logout');
});

Route::middleware(['auth', 'Checkrole'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil & Account Setting (semua role)
    Route::get('/profile',          [UserController::class, 'profile'])->name('profile.show');
    Route::post('/profile',         [UserController::class, 'updateProfile'])->name('profile.update');
    Route::get('/account-setting',  [UserController::class, 'accountSetting'])->name('account.setting');
    Route::post('/account-setting', [UserController::class, 'updatePassword'])->name('account.password');

    // Peminjaman
    Route::resource('/peminjaman', PeminjamanController::class)->except(['destroy']);
    // Admin: verifikasi, tolak, selesai
    Route::post('/peminjaman/{peminjaman}/verifikasi', [PeminjamanController::class, 'verifikasi'])->name('peminjaman.verifikasi');
    Route::post('/peminjaman/{peminjaman}/tolak',      [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');
    Route::post('/peminjaman/{peminjaman}/selesai',    [PeminjamanController::class, 'selesai'])->name('peminjaman.selesai');

    // Rekapitulasi (gabungan barang + ruangan)
    Route::get('/rekapitulasi',     [RekapitulasiController::class, 'index'])->name('rekapitulasi.index');
    Route::get('/rekapitulasi/pdf', [RekapitulasiController::class, 'pdf'])->name('rekapitulasi.pdf');

    // ===== Hanya Admin/Staf =====
    Route::middleware([])->group(function () {
        Route::resource('/barang',  BarangController::class);
        Route::delete('/fotobarang/{fotobarang}', [FotoBarangController::class, 'destroy'])->name('fotobarang.destroy');
        Route::resource('/ruangan', RuanganController::class);
        Route::delete('/fotoruangan/{fotoruangan}', [FotoRuanganController::class, 'destroy'])->name('fotoruangan.destroy');
        Route::resource('/pinjam',  PinjamController::class);
        Route::resource('/jabatan', JabatanController::class);
        Route::resource('/user',    UserController::class);
    });
});
