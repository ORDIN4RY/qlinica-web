<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\PegawaiController;
use App\Http\Controllers\AntrianController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\IcdxController;
use App\Http\Controllers\PresensiController;
use App\Http\Controllers\KomentarController;

// ── Public Routes ──
Route::get('/', [AuthController::class, 'showLogin'])->name('home');

Route::get('/login', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'pasien') {
            return redirect()->route('pasien.portal');
        }
        $firstMenu = $user->accessibleMenus()->keys()->first();
        return match ($firstMenu) {
            'Dashboard Admin'               => redirect()->route('beranda_admin'),
            'Dashboard Dokter'              => redirect()->route('dokter.dashboard'),
            'Dashboard Apoteker'            => redirect()->route('apoteker.dashboard'),
            'Resep Obat (Apoteker)'         => redirect()->route('apoteker.resep'),
            'Data Pasien'                   => $user->role === 'dokter' ? redirect()->route('dokter.pasien') : redirect()->route('admin.pasien'),
            'Antrian & Pemesanan'           => $user->role === 'dokter' ? redirect()->route('dokter.antrian') : redirect()->route('admin.pemesanan'),
            default                         => redirect()->route('beranda_admin'),
        };
    }
    return redirect('/');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/login-admin', [AuthController::class, 'showLoginPetugas'])->name('login.petugas');
Route::post('/login-admin', [AuthController::class, 'loginPetugas'])->name('login.petugas.submit');

// ── Dashboards ──
Route::middleware(['auth', 'menu:Dashboard Admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('beranda_admin');
});
Route::middleware(['auth', 'menu:Dashboard Dokter'])->group(function () {
    Route::get('/dokter/dashboard', [DokterController::class, 'dashboard'])->name('dokter.dashboard');
});
Route::middleware(['auth', 'menu:Dashboard Apoteker'])->group(function () {
    Route::get('/apoteker/dashboard', fn() => view('apoteker.dashboard'))->name('apoteker.dashboard');
});

// ── Antrian ──
Route::middleware(['auth', 'menu:Antrian & Pemesanan'])->group(function () {
    // Routes for Admin
    Route::get('/admin/pemesanan', [AntrianController::class, 'index'])->name('admin.pemesanan');
    Route::post('/admin/antrian', [AntrianController::class, 'store'])->name('admin.antrian.store')->middleware('menu:Antrian & Pemesanan,tambah');
    Route::patch('/admin/antrian/{id}/status', [AntrianController::class, 'updateStatus'])->name('admin.antrian.status')->middleware('menu:Antrian & Pemesanan,edit');
    Route::post('/admin/antrian/{id}/panggil', [AntrianController::class, 'panggilPeriksa'])->name('admin.antrian.panggil')->middleware('menu:Antrian & Pemesanan,edit');
    Route::patch('/admin/antrian/{id}/dilayani', [AntrianController::class, 'updateStatus'])->name('admin.antrian.dilayani')->middleware('menu:Antrian & Pemesanan,edit');
    Route::patch('/admin/antrian/{id}/selesai', [AntrianController::class, 'updateStatus'])->name('admin.antrian.selesai')->middleware('menu:Antrian & Pemesanan,edit');

    // Routes for Dokter
    Route::get('/dokter/antrian', [DokterController::class, 'antrianIndex'])->name('dokter.antrian');
    Route::patch('/dokter/antrian/{id}/panggil', [DokterController::class, 'panggilAntrian'])->name('dokter.antrian.panggil')->middleware('menu:Antrian & Pemesanan,edit');
});

// ── Pasien ──
Route::middleware(['auth', 'menu:Data Pasien'])->group(function () {
    // Routes for Admin
    Route::get('/admin/pasien', [PasienController::class, 'index'])->name('admin.pasien');
    Route::get('/admin/pasien/search', [PasienController::class, 'search'])->name('admin.pasien.search');
    Route::post('/admin/pasien', [PasienController::class, 'store'])->name('admin.pasien.store')->middleware('menu:Data Pasien,tambah');
    Route::put('/admin/pasien/{id}', [PasienController::class, 'update'])->name('admin.pasien.update')->middleware('menu:Data Pasien,edit');
    Route::delete('/admin/pasien/{id}', [PasienController::class, 'destroy'])->name('admin.pasien.destroy')->middleware('menu:Data Pasien,hapus');
    Route::post('/admin/pasien/{id}/create-account', [PasienController::class, 'createAccount'])->name('admin.pasien.create-account')->middleware('menu:Data Pasien,tambah');

    // Routes for Dokter
    Route::get('/dokter/pasien', [DokterController::class, 'pasienIndex'])->name('dokter.pasien');
});

// ── Pegawai ──
Route::middleware(['auth', 'menu:Data Pegawai (Admin)'])->group(function () {
    Route::get('/admin/pegawai', [PegawaiController::class, 'index'])->name('admin.pegawai');
    Route::get('/admin/pegawai/search', [PegawaiController::class, 'search'])->name('admin.pegawai.search');
    Route::post('/admin/pegawai', [PegawaiController::class, 'store'])->name('admin.pegawai.store')->middleware('menu:Data Pegawai (Admin),tambah');
    Route::put('/admin/pegawai/{id}', [PegawaiController::class, 'update'])->name('admin.pegawai.update')->middleware('menu:Data Pegawai (Admin),edit');
    Route::delete('/admin/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('admin.pegawai.destroy')->middleware('menu:Data Pegawai (Admin),hapus');
});

Route::middleware(['auth', 'menu:Presensi Pegawai (Admin)'])->group(function () {
    Route::get('/admin/presensi', [PresensiController::class, 'index'])->name('admin.presensi');
    Route::put('/admin/presensi/{id}', [PresensiController::class, 'update'])->name('admin.presensi.update')->middleware('menu:Presensi Pegawai (Admin),edit');
    Route::delete('/admin/presensi/{id}', [PresensiController::class, 'destroy'])->name('admin.presensi.destroy')->middleware('menu:Presensi Pegawai (Admin),hapus');
});

// ── Resep Obat (Apoteker) ──
Route::middleware(['auth', 'menu:Resep Obat (Apoteker)'])->group(function () {
    Route::get('/apoteker/resep', [ResepController::class, 'index'])->name('apoteker.resep');
    Route::patch('/apoteker/resep/{resep}', [ResepController::class, 'update'])->name('apoteker.resep.update')->middleware('menu:Resep Obat (Apoteker),edit');
});

// ── Data Obat (Apoteker) ──
Route::middleware(['auth', 'menu:Data Obat (Apoteker)'])->group(function () {
    Route::get('/apoteker/obat', fn() => view('apoteker.obat'))->name('apoteker.obat');
});

// ── Laporan Apotek (Apoteker) ──
Route::middleware(['auth', 'menu:Laporan Apotek (Apoteker)'])->group(function () {
    Route::get('/apoteker/laporan', fn() => view('apoteker.laporan'))->name('apoteker.laporan');
});

// ── ICDX ──
Route::middleware(['auth', 'menu:Data ICD-X (Admin)'])->group(function () {
    Route::get('/admin/icdx', [IcdxController::class, 'index'])->name('admin.icdx');
    Route::post('/admin/icdx', [IcdxController::class, 'store'])->name('admin.icdx.store')->middleware('menu:Data ICD-X (Admin),tambah');
    Route::put('/admin/icdx/{id}', [IcdxController::class, 'update'])->name('admin.icdx.update')->middleware('menu:Data ICD-X (Admin),edit');
    Route::delete('/admin/icdx/{id}', [IcdxController::class, 'destroy'])->name('admin.icdx.destroy')->middleware('menu:Data ICD-X (Admin),hapus');
});

// ── Laporan Penanganan (Admin) ──
Route::middleware(['auth', 'menu:Laporan Penanganan (Admin)'])->group(function () {
    Route::get('/admin/laporan/penanganan', [LaporanController::class, 'penanganan'])->name('admin.laporan.penanganan');
});

// ── Komentar & Feedback (Admin) ──
Route::middleware(['auth', 'menu:Komentar & Feedback (Admin)'])->group(function () {
    Route::get('/admin/komentar', [KomentarController::class, 'index'])->name('admin.komentar');
    Route::delete('/admin/komentar/{id}', [KomentarController::class, 'destroy'])->name('admin.komentar.destroy')->middleware('menu:Komentar & Feedback (Admin),hapus');
});

// ── Jabatan & Hak Akses (Admin) ──
Route::middleware(['auth', 'menu:Jabatan & Hak Akses (Admin)'])->group(function () {
    Route::get('/admin/jabatan', [JabatanController::class, 'index'])->name('admin.jabatan');
    Route::post('/admin/jabatan', [JabatanController::class, 'store'])->name('admin.jabatan.store')->middleware('menu:Jabatan & Hak Akses (Admin),tambah');
    Route::delete('/admin/jabatan/{id}', [JabatanController::class, 'destroy'])->name('admin.jabatan.destroy')->middleware('menu:Jabatan & Hak Akses (Admin),hapus');
    Route::put('/admin/jabatan/{id}/akses', [JabatanController::class, 'updateAkses'])->name('admin.jabatan.akses')->middleware('menu:Jabatan & Hak Akses (Admin),edit');
});

// ── Rekam Medis & Diagnosa (Dokter) ──
Route::middleware(['auth', 'menu:Rekam Medis & Diagnosa (Dokter)'])->group(function () {
    Route::post('/dokter/antrian/{antrianId}/diagnosa', [DokterController::class, 'simpanDiagnosa'])->name('dokter.antrian.diagnosa')->middleware('menu:Rekam Medis & Diagnosa (Dokter),tambah');
});

// ── Protected Routes Khusus Pasien ──
Route::middleware(['auth', 'role:pasien'])->group(function () {
    Route::get('/dashboard-pasien', function () {
        $user = auth()->user();
        $pasien = $user->pasien ?? null;
        $antrianAktif = null;
        if ($pasien) {
            $antrianAktif = \App\Models\Antrian::where('pasien_id', $pasien->id)
                ->where('tanggal', now()->toDateString())
                ->whereIn('status', ['Menunggu', 'Dipanggil'])
                ->first();
        }
        return view('dashboard_pasien', compact('user', 'pasien', 'antrianAktif'));
    })->name('pasien.portal');

    Route::post('/dashboard-pasien/antrian', [AntrianController::class, 'storePasien'])->name('pasien.antrian.store');
    Route::post('/dashboard-pasien/antrian/{id}/cancel', [AntrianController::class, 'cancelPasien'])->name('pasien.antrian.cancel');
    Route::get('/pemesanan', fn() => view('pemesanan', ['user' => auth()->user(), 'pasien' => auth()->user()->pasien]))->name('pemesanan.publik');
});
