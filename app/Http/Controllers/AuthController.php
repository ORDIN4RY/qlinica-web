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

        $user = auth()->user();
        $redirectTo = match ($firstMenu) {
            'Dashboard Admin'               => route('beranda_admin'),
            'Dashboard Dokter'              => route('dokter.dashboard'),
            'Dashboard Apoteker'            => route('apoteker.dashboard'),
            'Data Pasien'                   => ($user->role === 'dokter' ? route('dokter.pasien') : route('admin.pasien')),
            'Antrian & Pemesanan'           => ($user->role === 'dokter' ? route('dokter.antrian') : route('admin.pemesanan')),
            'Resep Obat (Apoteker)'         => route('apoteker.resep'),
            'Data Obat (Apoteker)'          => route('apoteker.obat'),
            'Data Pegawai (Admin)'          => route('admin.pegawai'),
            'Presensi Pegawai (Admin)'       => route('admin.presensi'),
            'Data ICD-X (Admin)'             => route('admin.icdx'),
            'Laporan Penanganan (Admin)'     => route('admin.laporan.penanganan'),
            'Komentar & Feedback (Admin)'    => route('admin.komentar'),
            'Jabatan & Hak Akses (Admin)'    => route('admin.jabatan'),
            default                         => route('beranda_admin'),
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

