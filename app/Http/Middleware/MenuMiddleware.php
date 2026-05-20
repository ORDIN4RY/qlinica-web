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
            // Dapatkan semua menu yang bisa diakses user
            $accessible = $user->accessibleMenus();

            foreach ($accessible as $name => $subAkses) {
                $route = match ($name) {
                    'Dashboard' => $user->hasMenuAccess('Dashboard', 'admin_dashboard') 
                        ? route('beranda_admin') 
                        : ($user->hasMenuAccess('Dashboard', 'dokter_dashboard') 
                            ? route('dokter.dashboard') 
                            : ($user->hasMenuAccess('Dashboard', 'apoteker_dashboard') 
                                ? route('apoteker.dashboard') 
                                : null)),
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
                    default               => null,
                };

                // Pastikan route yang dituju valid, tidak sama dengan current request,
                // dan tidak memicu loop
                if ($route) {
                    $routeCleaned = preg_replace('/[\r\n]/', '', $route);
                    $currentUrl = $request->url();
                    
                    if ($routeCleaned !== $currentUrl && $routeCleaned !== url()->current()) {
                        return redirect($routeCleaned)
                            ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
                    }
                }
            }

            // Jika tidak ada menu lain yang bisa dialihkan atau memicu redirect loop, abort 403
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
