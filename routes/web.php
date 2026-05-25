<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardPasienController;
use App\Http\Controllers\WelcomeController;
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
use App\Http\Controllers\AdminProfilController;
use App\Http\Controllers\ObatController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\KamarController;
use App\Http\Controllers\RawatInapController;
use App\Http\Controllers\MidtransWebhookController;

// Public
Route::get('/', [WelcomeController::class, 'index'])->name('home');

// ── Display Antrian Publik (tanpa login — untuk layar TV/monitor ruang tunggu) ──
Route::get('/antrian/display', [AntrianController::class, 'displayPage'])->name('antrian.display');
Route::get('/antrian/display/data', [AntrianController::class, 'displayData'])->name('antrian.display.data');


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

Route::middleware(['auth'])->group(function () {
    Route::get('/admin/profil',          [AdminProfilController::class, 'index'])->name('admin.profil');
    Route::put('/admin/profil',          [AdminProfilController::class, 'update'])->name('admin.profil.update');
    Route::put('/admin/profil/password', [AdminProfilController::class, 'updatePassword'])->name('admin.profil.password');
    Route::delete('/admin/profil/foto',  [AdminProfilController::class, 'deleteFoto'])->name('admin.profil.foto.delete');
});

// ── Dashboard ──
Route::middleware(['auth', 'menu:Dashboard'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('beranda_admin')->middleware('menu:Dashboard,admin_dashboard');
    Route::get('/apoteker/dashboard', [ObatController::class, 'dashboard'])->name('apoteker.dashboard')->middleware('menu:Dashboard,apoteker_dashboard');
    Route::get('/dokter/dashboard', [DokterController::class, 'dashboard'])->name('dokter.dashboard')->middleware('menu:Dashboard,dokter_dashboard');
});

// ── Antrian Pemesanan ──
Route::middleware(['auth', 'menu:Antrian Pemesanan'])->group(function () {
    Route::get('/admin/pemesanan', [AntrianController::class, 'index'])->name('admin.pemesanan');
    Route::get('/admin/antrian/realtime', [AntrianController::class, 'realtimeData'])->name('admin.antrian.realtime');
    Route::post('/admin/antrian', [AntrianController::class, 'store'])->name('admin.antrian.store')->middleware('menu:Antrian Pemesanan,tambah');
    Route::patch('/admin/antrian/{id}/status', [AntrianController::class, 'updateStatus'])->name('admin.antrian.status')->middleware('menu:Antrian Pemesanan,update');
    Route::post('/admin/antrian/{id}/panggil', [AntrianController::class, 'panggilPeriksa'])->name('admin.antrian.panggil')->middleware('menu:Antrian Pemesanan,update');
    Route::patch('/admin/antrian/{id}/dilayani', [AntrianController::class, 'updateStatus'])->name('admin.antrian.dilayani')->middleware('menu:Antrian Pemesanan,update');
    Route::patch('/admin/antrian/{id}/selesai', [AntrianController::class, 'updateStatus'])->name('admin.antrian.selesai')->middleware('menu:Antrian Pemesanan,update');
});

// ── Antrian Pemeriksaan ──
Route::middleware(['auth', 'menu:Antrian Pemeriksaan'])->group(function () {
    Route::get('/dokter/antrian', [DokterController::class, 'antrianIndex'])->name('dokter.antrian');
    Route::get('/dokter/antrian/{id}/periksa', [DokterController::class, 'periksa'])->name('dokter.antrian.periksa');
    Route::post('/dokter/antrian/{antrianId}/diagnosa', [DokterController::class, 'simpanDiagnosa'])->name('dokter.antrian.diagnosa');
});

// ── Rekam Medis ──
Route::middleware(['auth', 'menu:Rekam Medis'])->group(function () {
    Route::get('/dokter/pasien', [DokterController::class, 'pasienIndex'])->name('dokter.pasien');
    Route::get('/dokter/pasien/{id}', [DokterController::class, 'showPasien'])->name('dokter.pasien.show');
});

// ── Pasien ──
Route::middleware(['auth', 'menu:Pasien'])->group(function () {
    Route::get('/admin/pasien', [PasienController::class, 'index'])->name('admin.pasien');
    Route::get('/admin/pasien/search', [PasienController::class, 'search'])->name('admin.pasien.search');
    Route::post('/admin/pasien', [PasienController::class, 'store'])->name('admin.pasien.store')->middleware('menu:Pasien,tambah');
    Route::put('/admin/pasien/{id}', [PasienController::class, 'update'])->name('admin.pasien.update')->middleware('menu:Pasien,edit');
    Route::delete('/admin/pasien/{id}', [PasienController::class, 'destroy'])->name('admin.pasien.destroy')->middleware('menu:Pasien,hapus');
    Route::post('/admin/pasien/{id}/create-account', [PasienController::class, 'createAccount'])->name('admin.pasien.create-account')->middleware('menu:Pasien,tambah');

    // Route::get('/dokter/pasien', [DokterController::class, 'pasienIndex'])->name('dokter.pasien');
});

// ── Pegawai ──
Route::middleware(['auth', 'menu:Pegawai'])->group(function () {
    Route::get('/admin/pegawai', [PegawaiController::class, 'index'])->name('admin.pegawai');
    Route::get('/admin/pegawai/search', [PegawaiController::class, 'search'])->name('admin.pegawai.search');
    Route::post('/admin/pegawai', [PegawaiController::class, 'store'])->name('admin.pegawai.store')->middleware('menu:Pegawai,tambah');
    Route::put('/admin/pegawai/{id}', [PegawaiController::class, 'update'])->name('admin.pegawai.update')->middleware('menu:Pegawai,edit');
    Route::delete('/admin/pegawai/{id}', [PegawaiController::class, 'destroy'])->name('admin.pegawai.destroy')->middleware('menu:Pegawai,hapus');
});

// ── Presensi ──
Route::middleware(['auth', 'menu:Presensi'])->group(function () {
    Route::get('/admin/presensi', [PresensiController::class, 'index'])->name('admin.presensi');
    Route::post('/admin/presensi/shift/bulk', [PresensiController::class, 'bulkShift'])->name('admin.presensi.shift.bulk')->middleware('menu:Presensi,edit');
    Route::post('/admin/presensi/shift/pattern', [PresensiController::class, 'patternShift'])->name('admin.presensi.shift.pattern')->middleware('menu:Presensi,edit');
    Route::post('/admin/presensi/shift/copy', [PresensiController::class, 'copyShift'])->name('admin.presensi.shift.copy')->middleware('menu:Presensi,edit');
    Route::put('/admin/presensi/{id}', [PresensiController::class, 'update'])->name('admin.presensi.update')->middleware('menu:Presensi,edit');
    Route::put('/admin/presensi/{id}/shift', [PresensiController::class, 'updateShift'])->name('admin.presensi.shift')->middleware('menu:Presensi,edit');
    Route::delete('/admin/presensi/{id}', [PresensiController::class, 'destroy'])->name('admin.presensi.destroy')->middleware('menu:Presensi,hapus');
});

// ── Resep ──
Route::middleware(['auth', 'menu:Resep'])->group(function () {
    Route::get('/apoteker/resep', [ResepController::class, 'index'])->name('apoteker.resep');
    Route::patch('/apoteker/resep/{resep}', [ResepController::class, 'update'])->name('apoteker.resep.update')->middleware('menu:Resep,edit');
});

// ── Billing ──
Route::middleware(['auth', 'menu:Billing'])->group(function () {
    Route::get('/admin/billing', [BillingController::class, 'index'])->name('admin.billing');
    Route::get('/admin/billing/{billing}', [BillingController::class, 'show'])->name('admin.billing.show');
    Route::post('/admin/billing/{billing}/bayar', [BillingController::class, 'bayar'])->name('admin.billing.bayar')->middleware('menu:Billing,bayar');
    Route::post('/admin/billing/{billing}/cek-bpjs', [BillingController::class, 'cekBpjs'])->name('admin.billing.cek-bpjs')->middleware('menu:Billing,bpjs');
    // QRIS Midtrans
    Route::post('/admin/billing/{billing}/generate-qris', [BillingController::class, 'generateQris'])->name('admin.billing.generate-qris')->middleware('menu:Billing,bayar');
    Route::get('/admin/billing/{billing}/check-qris-status', [BillingController::class, 'checkQrisStatus'])->name('admin.billing.check-qris-status');
});

// ── Obat ──
Route::middleware(['auth', 'menu:Obat'])->group(function () {
    Route::get('/apoteker/obat',            [ObatController::class, 'index'])->name('apoteker.obat');
    Route::post('/apoteker/obat',           [ObatController::class, 'store'])->name('apoteker.obat.store')->middleware('menu:Obat,tambah');
    Route::put('/apoteker/obat/{id}',       [ObatController::class, 'update'])->name('apoteker.obat.update')->middleware('menu:Obat,edit');
    Route::delete('/apoteker/obat/{id}',    [ObatController::class, 'destroy'])->name('apoteker.obat.destroy')->middleware('menu:Obat,hapus');
    Route::post('/apoteker/obat/{id}/stok-opname', [ObatController::class, 'stokOpname'])->name('apoteker.obat.stok-opname')->middleware('menu:Obat,edit');
    Route::post('/apoteker/obat/{id}/restok', [ObatController::class, 'restok'])->name('apoteker.obat.restok')->middleware('menu:Obat,edit');
    Route::get('/apoteker/obat/{id}/riwayat-stok-opname', [ObatController::class, 'riwayatStokOpname'])->name('apoteker.obat.riwayat-stok-opname');
});

// ── ICDX ──
Route::middleware(['auth', 'menu:ICDX'])->group(function () {
    Route::get('/admin/icdx', [IcdxController::class, 'index'])->name('admin.icdx');
    Route::post('/admin/icdx', [IcdxController::class, 'store'])->name('admin.icdx.store')->middleware('menu:ICDX,tambah');
    Route::post('/admin/icdx/sync', [IcdxController::class, 'sync'])->name('admin.icdx.sync')->middleware('menu:ICDX,tambah');
    Route::put('/admin/icdx/{id}', [IcdxController::class, 'update'])->name('admin.icdx.update')->middleware('menu:ICDX,edit');
    Route::delete('/admin/icdx/{id}', [IcdxController::class, 'destroy'])->name('admin.icdx.destroy')->middleware('menu:ICDX,hapus');
});

// ── Laporan ──
Route::middleware(['auth', 'menu:Laporan'])->group(function () {
    Route::get('/admin/laporan',   function () { return view('laporan'); })->name('admin.laporan');
    Route::get('/admin/laporan/penanganan', [LaporanController::class, 'penanganan'])->name('admin.laporan.penanganan')->middleware('menu:Laporan,penanganan');
    Route::get('/admin/laporan/keuangan', [LaporanController::class, 'keuangan'])->name('admin.laporan.keuangan')->middleware('menu:Laporan,keuangan');
    Route::get('/apoteker/laporan', [ObatController::class, 'laporan'])->name('apoteker.laporan')->middleware('menu:Laporan,apotek');
});

// ── Manajemen Kamar ──
Route::middleware(['auth', 'menu:Kamar'])->group(function () {
    Route::get('/admin/kamar', [KamarController::class, 'index'])->name('admin.kamar');
    Route::post('/admin/kamar', [KamarController::class, 'store'])->name('admin.kamar.store')->middleware('menu:Kamar,tambah');
    Route::put('/admin/kamar/{id}', [KamarController::class, 'update'])->name('admin.kamar.update')->middleware('menu:Kamar,edit');
    Route::delete('/admin/kamar/{id}', [KamarController::class, 'destroy'])->name('admin.kamar.destroy')->middleware('menu:Kamar,hapus');
});

// ── Rawat Inap ──
Route::middleware(['auth', 'menu:Rawat Inap'])->group(function () {
    Route::get('/admin/rawat_inap', [RawatInapController::class, 'index'])->name('admin.rawat_inap');
    Route::post('/admin/rawat_inap', [RawatInapController::class, 'store'])->name('admin.rawat_inap.store')->middleware('menu:Rawat Inap,tambah');
    Route::post('/admin/rawat_inap/{id}/checkout', [RawatInapController::class, 'checkout'])->name('admin.rawat_inap.checkout')->middleware('menu:Rawat Inap,edit');
    Route::post('/admin/rawat_inap/{id}/pindah', [RawatInapController::class, 'pindahKamar'])->name('admin.rawat_inap.pindah')->middleware('menu:Rawat Inap,edit');
    Route::post('/admin/rawat_inap/{id}/resep', [RawatInapController::class, 'storeResep'])->name('admin.rawat_inap.resep')->middleware('menu:Rawat Inap,edit');
});

// ── Komentar ──
Route::middleware(['auth', 'menu:Komentar'])->group(function () {
    Route::get('/admin/komentar', [KomentarController::class, 'index'])->name('admin.komentar');
    Route::delete('/admin/komentar/{id}', [KomentarController::class, 'destroy'])->name('admin.komentar.destroy')->middleware('menu:Komentar,hapus');
});

// ── Jabatan & Hak Akses (hanya Admin) ──
Route::middleware(['auth', 'menu:Jabatan'])->group(function () {
    Route::get('/admin/jabatan', [JabatanController::class, 'index'])->name('admin.jabatan');
    Route::post('/admin/jabatan', [JabatanController::class, 'store'])->name('admin.jabatan.store')->middleware('menu:Jabatan,tambah');
    Route::delete('/admin/jabatan/{id}', [JabatanController::class, 'destroy'])->name('admin.jabatan.destroy')->middleware('menu:Jabatan,hapus');
    Route::put('/admin/jabatan/{id}/akses', [JabatanController::class, 'updateAkses'])->name('admin.jabatan.akses')->middleware('menu:Jabatan,edit');
});


// ── Protected Routes Khusus Pasien ──
Route::middleware(['auth', 'role:pasien'])->group(function () {
    Route::get('/dashboard-pasien', [DashboardPasienController::class, 'portal'])->name('pasien.portal');

    Route::post('/dashboard-pasien/antrian', [AntrianController::class, 'storePasien'])->name('pasien.antrian.store');
    Route::post('/dashboard-pasien/antrian/{id}/cancel', [AntrianController::class, 'cancelPasien'])->name('pasien.antrian.cancel');
    Route::post('/dashboard-pasien/feedback', [AntrianController::class, 'storeFeedback'])->name('pasien.antrian.feedback');
    Route::get('/dashboard-pasien/riwayat/{id}', [AntrianController::class, 'showRiwayatDetail'])->name('pasien.riwayat.detail');
    Route::put('/dashboard-pasien/profil', function (\Illuminate\Http\Request $request) {
        $user   = auth()->user();
        $pasien = $user->pasien;
        if (!$pasien) {
            return response()->json(['success' => false, 'message' => 'Data pasien tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'nama'          => 'required|string|max:100',
            'nik'           => 'nullable|string|size:16|unique:pasien,nik,' . $pasien->id,
            'tgl_lahir'     => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'golongan_darah'=> 'nullable|in:A,B,AB,O',
            'alamat'        => 'nullable|string|max:255',
            'desa'          => 'nullable|string|max:50',
            'kota'          => 'nullable|string|max:50',
            'nama_kk'       => 'nullable|string|max:100',
            'riwayat_alergi'=> 'nullable|string',
        ]);

        $pasien->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'pasien'  => [
                'nama'          => $pasien->nama,
                'nik'           => $pasien->nik,
                'tgl_lahir'     => $pasien->tgl_lahir?->format('Y-m-d'),
                'jenis_kelamin' => $pasien->jenis_kelamin,
                'golongan_darah'=> $pasien->golongan_darah,
                'alamat'        => $pasien->alamat,
                'desa'          => $pasien->desa,
                'kota'          => $pasien->kota,
                'nama_kk'       => $pasien->nama_kk,
                'riwayat_alergi'=> $pasien->riwayat_alergi,
            ],
        ]);
    })->name('pasien.profil.update');

    // ── Polling: status antrian realtime ──
    Route::get('/dashboard-pasien/antrian/status', function () {
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;

        $today = now()->toDateString();

        $dilayani = \App\Models\Antrian::where('tanggal', $today)
            ->where('status', 'Dipanggil')
            ->orderBy('updated_at', 'desc')
            ->first();

        $total    = \App\Models\Antrian::where('tanggal', $today)->count();
        $selesai  = \App\Models\Antrian::where('tanggal', $today)->where('status', 'Selesai')->count();
        $menunggu = \App\Models\Antrian::where('tanggal', $today)->whereIn('status', ['Menunggu', 'Dipanggil'])->count();

        $daftarMenunggu = \App\Models\Antrian::with('pasien')
            ->where('tanggal', $today)
            ->whereIn('status', ['Menunggu', 'Dipanggil'])
            ->orderBy('no_antrian', 'asc')
            ->get()
            ->map(fn($a) => [
                'no_antrian' => str_pad($a->no_antrian, 3, '0', STR_PAD_LEFT),
                'nama'       => $a->pasien->nama ?? '-',
                'jenis'      => $a->jenis,
                'status'     => $a->status,
            ]);

        // Antrian aktif milik pasien ini
        $antrianAktif = null;
        $pasienSelesaiHariIni = false;
        if ($pasien) {
            $aktif = \App\Models\Antrian::where('pasien_id', $pasien->id)
                ->where('tanggal', $today)
                ->whereIn('status', ['Menunggu', 'Dipanggil', 'Dilayani'])
                ->first();
            if ($aktif) {
                $antrianAktif = [
                    'id'         => $aktif->id,
                    'no_antrian' => str_pad($aktif->no_antrian, 3, '0', STR_PAD_LEFT),
                    'jenis'      => $aktif->jenis,
                    'status'     => $aktif->status,
                ];
            }
            $pasienSelesaiHariIni = \App\Models\Antrian::where('pasien_id', $pasien->id)
                ->where('tanggal', $today)
                ->where('status', 'Selesai')
                ->exists();
        }

        return response()->json([
            'dilayani'       => $dilayani ? [
                'no_antrian' => str_pad($dilayani->no_antrian, 3, '0', STR_PAD_LEFT),
                'jenis'      => $dilayani->jenis,
            ] : null,
            'total'          => $total,
            'selesai'        => $selesai,
            'menunggu'       => $menunggu,
            'daftar_menunggu'=> $daftarMenunggu,
            'antrian_aktif'  => $antrianAktif,
            'pasien_selesai_hari_ini' => $pasienSelesaiHariIni,
        ]);
    })->name('pasien.antrian.status');

    Route::get('/pemesanan', function () {
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;
        return view('pemesanan', compact('user', 'pasien'));
    })->name('pemesanan.publik');
});


