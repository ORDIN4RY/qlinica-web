<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /** Tampilkan halaman welcome (landing). */
    public function showLogin()
    {
        return view('welcome');
    }

    /** Tampilkan halaman login petugas. */
    public function showLoginPetugas()
    {
        return view('login_petugas');
    }

    /** Proses login pasien menggunakan No. Rekam Medik. */
    public function login(Request $request)
    {
        $request->validate([
            'login_id' => 'required|string',
            'password' => 'required',
        ], [
            'login_id.required' => 'No. Rekam Medik wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginId = $request->login_id;
        $user    = null;

        // Cari di tabel pasien berdasarkan no_rm
        $pasien = Pasien::with('user')->where('no_rm', $loginId)->first();
        if ($pasien) {
            $user = $pasien->user;
        }

        if (!$user) {
            return back()
                ->withErrors(['login_id' => 'No. Rekam Medik tidak ditemukan.'])
                ->withInput($request->only('login_id'));
        }

        // Pastikan hanya pasien yang bisa login di form ini
        if ($user->role !== 'pasien') {
            return back()
                ->withErrors(['login_id' => 'Akun ini bukan akun pasien. Gunakan halaman Login Petugas.'])
                ->withInput($request->only('login_id'));
        }

        // Verifikasi password
        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['login_id' => 'No. Rekam Medik atau password salah.'])
                ->withInput($request->only('login_id'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        session(['last_activity_time' => time()]);

        return redirect(route('pasien.portal'));

    }

    /** Proses login petugas/admin menggunakan email. */
    public function loginPetugas(Request $request)
    {
        $request->validate([
            'login_id' => 'required|email',
            'password' => 'required',
        ], [
            'login_id.required' => 'Email wajib diisi.',
            'login_id.email'    => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $request->login_id)->first();

        if (!$user) {
            return back()
                ->withErrors(['login_id' => 'Email tidak terdaftar sebagai petugas.'])
                ->withInput($request->only('login_id'));
        }

        // Pastikan bukan pasien yang mencoba masuk lewat form petugas
        if ($user->role === 'pasien') {
            return back()
                ->withErrors(['login_id' => 'Akun ini adalah akun pasien. Silakan login melalui halaman utama.'])
                ->withInput($request->only('login_id'));
        }

        // Verifikasi password
        if (!Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['login_id' => 'Email atau password salah.'])
                ->withInput($request->only('login_id'));
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        session(['last_activity_time' => time()]);

        // Auto-redirect berdasarkan menu pertama yang bisa diakses
        $menus = $user->accessibleMenus();
        $firstMenu = $menus->keys()->first();

        $redirectTo = match ($firstMenu) {
            'Dashboard' => $user->hasMenuAccess('Dashboard', 'admin_dashboard') 
                ? route('beranda_admin') 
                : ($user->hasMenuAccess('Dashboard', 'dokter_dashboard') 
                    ? route('dokter.dashboard') 
                    : ($user->hasMenuAccess('Dashboard', 'apoteker_dashboard') 
                        ? route('apoteker.dashboard') 
                        : route('beranda_admin'))),
            'Pasien'              => route('admin.pasien'),
            'Pegawai'             => route('admin.pegawai'),
            'Antrian Pemesanan'   => route('admin.pemesanan'),
            'Resep'               => route('apoteker.resep'),
            'Obat'                => route('apoteker.obat'),
            'ICDX'                => route('admin.icdx'),
            'Laporan'             => route('admin.laporan'),
            'Komentar'            => route('admin.komentar'),
            'Rekam Medis'         => route('dokter.pasien'),
            'Jabatan'             => route('admin.jabatan'),
            'Presensi'            => route('admin.presensi'),
            'Antrian Pemeriksaan' => route('dokter.antrian'),
            'Billing'             => route('admin.billing'),
            'Kamar'               => route('admin.kamar'),
            'Rawat Inap'          => route('admin.rawat_inap'),
            default               => route('beranda_admin'),
        };

        return redirect($redirectTo);
    }

    /** Logout. */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

