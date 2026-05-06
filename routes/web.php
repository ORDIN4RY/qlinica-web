<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\DokterController;

use App\Http\Controllers\IcdxController;

// Public
Route::get('/', [AuthController::class, 'showLogin'])->name('home');

// GET /login — redirect ke halaman sesuai agar tidak terjadi MethodNotAllowedHttpException
// Laravel kadang redirect ke /login secara default saat auth gagal
Route::get('/login', function () {
    // Jika sudah login, arahkan ke halaman yang sesuai dengan role
    if (auth()->check()) {
        $role = auth()->user()->role;
        return match($role) {
            'admin' => redirect()->route('beranda_admin'),
            'dokter' => redirect()->route('dokter.dashboard'),
            'apoteker' => redirect()->route('apoteker.dashboard'),
            'pasien' => redirect()->route('pasien.portal'),
            default => redirect('/')
        };
    }
    // Belum login → arahkan ke landing page (login pasien)
    return redirect('/');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Login Petugas
Route::get('/login-admin', [AuthController::class, 'showLoginPetugas'])->name('login.petugas');
Route::post('/login-admin', [AuthController::class, 'loginPetugas'])->name('login.petugas.submit');

// Protected Routes Khusus Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('beranda_admin');
    Route::get('/admin/pemesanan', [AntrianController::class, 'index'])->name('admin.pemesanan');
    Route::post('/admin/antrian', [AntrianController::class, 'store'])->name('admin.antrian.store');
    Route::patch('/admin/antrian/{id}/status', [AntrianController::class, 'updateStatus'])->name('admin.antrian.status');
    Route::post('/admin/antrian/{id}/panggil', [AntrianController::class, 'panggilPeriksa'])->name('admin.antrian.panggil');
    Route::patch('/admin/antrian/{id}/dilayani', [AntrianController::class, 'updateStatus'])->name('admin.antrian.dilayani');
    Route::patch('/admin/antrian/{id}/selesai', [AntrianController::class, 'updateStatus'])->name('admin.antrian.selesai');
    Route::get('/admin/komentar',  function () { return view('admin.komentar'); })->name('admin.komentar');
    Route::get('/admin/laporan',   function () { return view('laporan'); })->name('admin.laporan');

    // ICDX
    Route::get('/admin/icdx', [IcdxController::class, 'index'])->name('admin.icdx');

    // Pegawai CRUD
    Route::get('/admin/pegawai', [PegawaiController::class, 'index'])->name('admin.pegawai');
    Route::get('/admin/pegawai/search', [PegawaiController::class, 'search'])->name('admin.pegawai.search');
    Route::post('/admin/pegawai', [PegawaiController::class, 'store'])->name('admin.pegawai.store');
    Route::put('/admin/pegawai/{id}', [PegawaiController::class, 'update'])->name('admin.pegawai.update');
    Route::delete('/admin/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('admin.pegawai.destroy');

    // Pasien CRUD
    Route::get('/admin/pasien',         [PasienController::class, 'index'])->name('admin.pasien');
    Route::get('/admin/pasien/search',  [PasienController::class, 'search'])->name('admin.pasien.search');
    Route::post('/admin/pasien',        [PasienController::class, 'store'])->name('admin.pasien.store');
    Route::put('/admin/pasien/{id}',    [PasienController::class, 'update'])->name('admin.pasien.update');
    Route::delete('/admin/pasien/{id}', [PasienController::class, 'destroy'])->name('admin.pasien.destroy');
    Route::post('/admin/pasien/{id}/create-account', [PasienController::class, 'createAccount'])->name('admin.pasien.create-account');

    // ICDX
    Route::get('/admin/icdx',              [IcdxController::class, 'index'])->name('admin.icdx');
    Route::post('/admin/icdx',             [IcdxController::class, 'store'])->name('admin.icdx.store');
    Route::put('/admin/icdx/{id}',         [IcdxController::class, 'update'])->name('admin.icdx.update');
    Route::delete('/admin/icdx/{id}',      [IcdxController::class, 'destroy'])->name('admin.icdx.destroy');
});

// Protected Routes Khusus Pasien
Route::middleware(['auth', 'role:pasien'])->group(function () {
    Route::get('/dashboard-pasien', function () {
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;
        return view('dashboard_pasien', compact('user', 'pasien'));
    })->name('pasien.portal');

    Route::post('/dashboard-pasien/antrian', [AntrianController::class, 'storePasien'])->name('pasien.antrian.store');

    Route::get('/pemesanan', function () { 
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;
        return view('pemesanan', compact('user', 'pasien'));
    })->name('pemesanan.publik');
});

// Protected Routes Khusus Apoteker
Route::middleware(['auth', 'role:apoteker'])->group(function () {
    Route::get('/apoteker/dashboard', function () { 
        return view('apoteker.dashboard'); 
    })->name('apoteker.dashboard');
    
    Route::get('/apoteker/obat', function () { 
        return view('apoteker.obat'); 
    })->name('apoteker.obat');
    
    Route::get('/apoteker/resep', [ResepController::class, 'index'])->name('apoteker.resep');
    Route::patch('/apoteker/resep/{resep}', [ResepController::class, 'update'])->name('apoteker.resep.update');

    Route::get('/apoteker/laporan', function () { 
        return view('apoteker.laporan'); 
    })->name('apoteker.laporan');
});

// Protected Routes Khusus Dokter
Route::middleware(['auth', 'role:dokter'])->group(function () {
    Route::get('/dokter/dashboard', [DokterController::class, 'dashboard'])->name('dokter.dashboard');
    Route::get('/dokter/antrian', [DokterController::class, 'antrianIndex'])->name('dokter.antrian');
    Route::patch('/dokter/antrian/{id}/panggil', [DokterController::class, 'panggilAntrian'])->name('dokter.antrian.panggil');
    Route::post('/dokter/antrian/{antrianId}/diagnosa', [DokterController::class, 'simpanDiagnosa'])->name('dokter.antrian.diagnosa');
    Route::get('/dokter/pasien', [DokterController::class, 'pasienIndex'])->name('dokter.pasien');
});



