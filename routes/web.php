<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;

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
    Route::get('/admin/pasien',    function () { return view('pasien'); })->name('admin.pasien');
    Route::get('/admin/pemesanan', function () { return view('pemesanan'); })->name('admin.pemesanan');
    Route::get('/admin/pegawai',   function () { return view('pegawai'); })->name('admin.pegawai');
    Route::get('/admin/komentar',  function () { return view('komentar'); })->name('admin.komentar');
    Route::get('/admin/laporan',   function () { return view('laporan'); })->name('admin.laporan');
    Route::resource('patients', PatientController::class);
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

