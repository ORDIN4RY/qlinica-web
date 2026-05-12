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
                'Dashboard' => route('beranda_admin'),
                'Pasien' => route('admin.pasien'),
                'Pegawai' => route('admin.pegawai'),
                'Antrian' => route('admin.pemesanan'),
                'Resep' => route('apoteker.resep'),
                'Obat' => route('apoteker.obat'),
                'ICDX' => route('admin.icdx'),
                'Laporan' => route('admin.laporan'),
                'Komentar' => route('admin.komentar'),
                'Jabatan' => route('admin.jabatan'),
                default => redirect('/'),
            };
            return redirect($route)
                ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
