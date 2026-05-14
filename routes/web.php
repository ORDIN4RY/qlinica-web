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

// Public
Route::get('/', [AuthController::class, 'showLogin'])->name('home');

// GET /login — redirect ke halaman sesuai menu akses user
Route::get('/login', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->role === 'pasien') {
            return redirect()->route('pasien.portal');
        }
        // Redirect ke menu pertama yang bisa diakses pegawai
        $firstMenu = $user->accessibleMenus()->keys()->first();
        return match ($firstMenu) {
            'Dashboard' => redirect()->route('beranda_admin'),
            'Resep' => redirect()->route('apoteker.resep'),
            'Obat' => redirect()->route('apoteker.obat'),
            default => redirect()->route('beranda_admin'),
        };
    }
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
    Route::get('/admin/komentar', [KomentarController::class, 'index'])->name('admin.komentar');
    Route::delete('/admin/komentar/{id}', [KomentarController::class, 'destroy'])->name('admin.komentar.destroy');

    // Laporan
    Route::get('/admin/laporan',   function () { return view('laporan'); })->name('admin.laporan');
    Route::get('/admin/laporan/penanganan', [LaporanController::class, 'penanganan'])->name('admin.laporan.penanganan');
});

// ── Dashboard ──
Route::middleware(['auth', 'menu:Dashboard'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('beranda_admin');
    Route::get('/apoteker/dashboard', fn() => view('apoteker.dashboard'))->name('apoteker.dashboard');
    Route::get('/dokter/dashboard', [DokterController::class, 'dashboard'])->name('dokter.dashboard');
});

// ── Antrian ──
Route::middleware(['auth', 'menu:Antrian'])->group(function () {
    Route::get('/admin/pemesanan', [AntrianController::class, 'index'])->name('admin.pemesanan');
    Route::post('/admin/antrian', [AntrianController::class, 'store'])->name('admin.antrian.store')->middleware('menu:Antrian,tambah');
    Route::patch('/admin/antrian/{id}/status', [AntrianController::class, 'updateStatus'])->name('admin.antrian.status')->middleware('menu:Antrian,edit');
    Route::post('/admin/antrian/{id}/panggil', [AntrianController::class, 'panggilPeriksa'])->name('admin.antrian.panggil')->middleware('menu:Antrian,edit');
    Route::patch('/admin/antrian/{id}/dilayani', [AntrianController::class, 'updateStatus'])->name('admin.antrian.dilayani')->middleware('menu:Antrian,edit');
    Route::patch('/admin/antrian/{id}/selesai', [AntrianController::class, 'updateStatus'])->name('admin.antrian.selesai')->middleware('menu:Antrian,edit');

    Route::get('/dokter/antrian', [DokterController::class, 'antrianIndex'])->name('dokter.antrian');
    Route::patch('/dokter/antrian/{id}/panggil', [DokterController::class, 'panggilAntrian'])->name('dokter.antrian.panggil')->middleware('menu:Antrian,edit');
});

// ── Pasien ──
Route::middleware(['auth', 'menu:Pasien'])->group(function () {
    Route::get('/admin/pasien', [PasienController::class, 'index'])->name('admin.pasien');
    Route::get('/admin/pasien/search', [PasienController::class, 'search'])->name('admin.pasien.search');
    Route::post('/admin/pasien', [PasienController::class, 'store'])->name('admin.pasien.store')->middleware('menu:Pasien,tambah');
    Route::put('/admin/pasien/{id}', [PasienController::class, 'update'])->name('admin.pasien.update')->middleware('menu:Pasien,edit');
    Route::delete('/admin/pasien/{id}', [PasienController::class, 'destroy'])->name('admin.pasien.destroy')->middleware('menu:Pasien,hapus');
    Route::post('/admin/pasien/{id}/create-account', [PasienController::class, 'createAccount'])->name('admin.pasien.create-account')->middleware('menu:Pasien,tambah');

    Route::get('/dokter/pasien', [DokterController::class, 'pasienIndex'])->name('dokter.pasien');
});

// ── Pegawai ──
Route::middleware(['auth', 'menu:Pegawai'])->group(function () {
    Route::get('/admin/pegawai', [PegawaiController::class, 'index'])->name('admin.pegawai');
    Route::get('/admin/pegawai/search', [PegawaiController::class, 'search'])->name('admin.pegawai.search');
    Route::post('/admin/pegawai', [PegawaiController::class, 'store'])->name('admin.pegawai.store')->middleware('menu:Pegawai,tambah');
    Route::put('/admin/pegawai/{id}', [PegawaiController::class, 'update'])->name('admin.pegawai.update')->middleware('menu:Pegawai,edit');
    Route::delete('/admin/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('admin.pegawai.destroy')->middleware('menu:Pegawai,hapus');
    Route::get('/admin/presensi', [PresensiController::class, 'index'])->name('admin.presensi');
    Route::put('/admin/presensi/{id}', [PresensiController::class, 'update'])->name('admin.presensi.update');
    Route::delete('/admin/presensi/{id}', [PresensiController::class, 'destroy'])->name('admin.presensi.destroy');

    });

// ── Resep ──
Route::middleware(['auth', 'menu:Resep'])->group(function () {
    Route::get('/apoteker/resep', [ResepController::class, 'index'])->name('apoteker.resep');
    Route::patch('/apoteker/resep/{resep}', [ResepController::class, 'update'])->name('apoteker.resep.update')->middleware('menu:Resep,edit');
});

// ── Obat ──
Route::middleware(['auth', 'menu:Obat'])->group(function () {
    Route::get('/apoteker/obat', fn() => view('apoteker.obat'))->name('apoteker.obat');
});

// ── ICDX ──
Route::middleware(['auth', 'menu:ICDX'])->group(function () {
    Route::get('/admin/icdx', [IcdxController::class, 'index'])->name('admin.icdx');
    Route::post('/admin/icdx', [IcdxController::class, 'store'])->name('admin.icdx.store')->middleware('menu:ICDX,tambah');
    Route::put('/admin/icdx/{id}', [IcdxController::class, 'update'])->name('admin.icdx.update')->middleware('menu:ICDX,edit');
    Route::delete('/admin/icdx/{id}', [IcdxController::class, 'destroy'])->name('admin.icdx.destroy')->middleware('menu:ICDX,hapus');
});

// ── Laporan ──
Route::middleware(['auth', 'menu:Laporan'])->group(function () {
    Route::get('/admin/laporan',   function () { return view('laporan'); })->name('admin.laporan');
    Route::get('/admin/laporan/penanganan', [LaporanController::class, 'penanganan'])->name('admin.laporan.penanganan');
});

// ── Komentar ──
Route::middleware(['auth', 'menu:Komentar'])->group(function () {
    Route::get('/admin/komentar', [KomentarController::class, 'index'])->name('admin.komentar');
    Route::delete('/admin/komentar/{id}', [KomentarController::class, 'destroy'])->name('admin.komentar.destroy');
});

// ── Jabatan & Hak Akses (hanya Admin) ──
Route::middleware(['auth', 'menu:Jabatan'])->group(function () {
    Route::get('/admin/jabatan', [JabatanController::class, 'index'])->name('admin.jabatan');
    Route::post('/admin/jabatan', [JabatanController::class, 'store'])->name('admin.jabatan.store')->middleware('menu:Jabatan,tambah');
    Route::delete('/admin/jabatan/{id}', [JabatanController::class, 'destroy'])->name('admin.jabatan.destroy')->middleware('menu:Jabatan,hapus');
    Route::put('/admin/jabatan/{id}/akses', [JabatanController::class, 'updateAkses'])->name('admin.jabatan.akses')->middleware('menu:Jabatan,edit');
});

// ── Rekam Medis ──
Route::middleware(['auth', 'menu:Rekam Medis'])->group(function () {
    Route::post('/dokter/antrian/{antrianId}/diagnosa', [DokterController::class, 'simpanDiagnosa'])->name('dokter.antrian.diagnosa')->middleware('menu:Rekam Medis,tambah');
});

// ── Protected Routes Khusus Pasien ──
Route::middleware(['auth', 'role:pasien'])->group(function () {
    Route::get('/dashboard-pasien', function () {
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;

        $antrianAktif = null;
        $totalAntrianHariIni = 0;
        $antrianSelesai = 0;
        $antrianMenunggu = 0;
        $antrianDilayani = null;
        $antrianPasienMenunggu = collect();

        if ($pasien) {
            $antrianAktif = \App\Models\Antrian::where('pasien_id', $pasien->id)
                ->where('tanggal', now()->toDateString())
                ->whereIn('status', ['Menunggu', 'Dipanggil'])
                ->first();

            $totalAntrianHariIni = \App\Models\Antrian::where('tanggal', now()->toDateString())->count();
            $antrianSelesai = \App\Models\Antrian::where('tanggal', now()->toDateString())->where('status', 'Selesai')->count();
            $antrianMenunggu = \App\Models\Antrian::where('tanggal', now()->toDateString())->whereIn('status', ['Menunggu', 'Dipanggil'])->count();
            $antrianDilayani = \App\Models\Antrian::where('tanggal', now()->toDateString())->where('status', 'Dipanggil')->orderBy('updated_at', 'desc')->first();
            $antrianPasienMenunggu = \App\Models\Antrian::with('pasien')->where('tanggal', now()->toDateString())->whereIn('status', ['Menunggu', 'Dipanggil'])->orderBy('no_antrian', 'asc')->get();
        }

        return view('dashboard_pasien', compact('user', 'pasien', 'antrianAktif', 'totalAntrianHariIni', 'antrianSelesai', 'antrianMenunggu', 'antrianDilayani', 'antrianPasienMenunggu'));
    })->name('pasien.portal');

    Route::post('/dashboard-pasien/antrian', [AntrianController::class, 'storePasien'])->name('pasien.antrian.store');
    Route::post('/dashboard-pasien/antrian/{id}/cancel', [AntrianController::class, 'cancelPasien'])->name('pasien.antrian.cancel');

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
