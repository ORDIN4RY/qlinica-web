<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PegawaiController;

// Public
Route::get('/', [AuthController::class, 'showLogin'])->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Login Petugas
Route::get('/login-petugas', [AuthController::class, 'showLoginPetugas'])->name('login.petugas');
Route::post('/login-petugas', [AuthController::class, 'loginPetugas'])->name('login.petugas.submit');


// Protected Routes Khusus Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('beranda_admin');
    Route::get('/admin/pemesanan', function () { return view('pemesanan'); })->name('admin.pemesanan');
    Route::get('/admin/komentar',  function () { return view('komentar'); })->name('admin.komentar');
    Route::get('/admin/laporan',   function () { return view('laporan'); })->name('admin.laporan');

    // Pasien CRUD
    Route::get('/admin/pasien',         [PasienController::class, 'index'])->name('admin.pasien');
    Route::post('/admin/pasien',        [PasienController::class, 'store'])->name('admin.pasien.store');
    Route::put('/admin/pasien/{id}',    [PasienController::class, 'update'])->name('admin.pasien.update');
    Route::delete('/admin/pasien/{id}', [PasienController::class, 'destroy'])->name('admin.pasien.destroy');

    // Pegawai CRUD
    Route::get('/admin/pegawai',              [PegawaiController::class, 'index'])->name('admin.pegawai');
    Route::get('/admin/pegawai/data',         [PegawaiController::class, 'fetchAll'])->name('admin.pegawai.data');
    Route::post('/admin/pegawai',             [PegawaiController::class, 'store'])->name('admin.pegawai.store');
    Route::put('/admin/pegawai/{id}',         [PegawaiController::class, 'update'])->name('admin.pegawai.update');
    Route::delete('/admin/pegawai/{id}',      [PegawaiController::class, 'destroy'])->name('admin.pegawai.destroy');
});

// Protected Routes Khusus Pasien
Route::middleware(['auth', 'role:pasien'])->group(function () {
    Route::get('/dashboard-pasien', function () {
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;
        return view('dashboard_pasien', compact('user', 'pasien'));
    })->name('pasien.portal');

    Route::get('/pemesanan', function () {
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;
        return view('pemesanan', compact('user', 'pasien'));
    })->name('pemesanan.publik');
});

