<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class MenuMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $menuName   Nama menu yang diakses (misal: 'Pasien', 'Pegawai')
     * @param  string|null $level  Tingkat akses: lihat | tambah | edit | hapus
     */
    public function handle(Request $request, Closure $next, string $menuName, ?string $level = null): Response
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();

        // Pasien hanya bisa akses route pasien (dijamin route-group tersendiri)
        if ($user->role === 'pasien') {
            return redirect()->route('pasien.portal');
        }

        // Cek akses menu berdasarkan jabatan pegawai
        if (!$user->hasMenuAccess($menuName, $level)) {
            // Redirect ke halaman yang sesuai berdasarkan menu pertama yang bisa diakses
            $firstMenu = $user->accessibleMenus()->keys()->first();
            $route = match ($firstMenu) {
                'Dashboard Admin'               => route('beranda_admin'),
                'Dashboard Dokter'              => route('dokter.dashboard'),
                'Dashboard Apoteker'            => route('apoteker.dashboard'),
                'Data Pasien'                   => ($user->role === 'dokter' ? route('dokter.pasien') : route('admin.pasien')),
                'Antrian & Pemesanan'           => ($user->role === 'dokter' ? route('dokter.antrian') : route('admin.pemesanan')),
                'Rekam Medis & Diagnosa (Dokter)' => route('dokter.antrian'),
                'Resep Obat (Apoteker)'         => route('apoteker.resep'),
                'Data Obat (Apoteker)'          => route('apoteker.obat'),
                'Laporan Apotek (Apoteker)'      => route('apoteker.laporan'),
                'Data Pegawai (Admin)'          => route('admin.pegawai'),
                'Presensi Pegawai (Admin)'       => route('admin.presensi'),
                'Data ICD-X (Admin)'             => route('admin.icdx'),
                'Laporan Penanganan (Admin)'     => route('admin.laporan.penanganan'),
                'Komentar & Feedback (Admin)'    => route('admin.komentar'),
                'Jabatan & Hak Akses (Admin)'    => route('admin.jabatan'),
                default                         => route('home'),
            };
            // Sanitize to prevent HTTP header injection (newlines in URL)
            $route = preg_replace('/[\r\n]/', '', $route);
            return redirect($route)
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
