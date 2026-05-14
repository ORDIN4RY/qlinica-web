<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SessionTimeout
{
    /**
     * Durasi maksimal session aktif (dalam detik).
     * Default: 3600 detik = 1 jam
     */
    protected int $timeoutSeconds = 3600;

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session('last_activity_time');

            if ($lastActivity !== null && (time() - $lastActivity) > $this->timeoutSeconds) {
                // Simpan info SEBELUM logout agar bisa redirect ke halaman yang benar
                $user = Auth::user();
                $isPasien = $user->role === 'pasien';

                // Paksa logout
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $redirectTo = $isPasien ? '/' : route('login.petugas');

                return redirect($redirectTo)
                    ->withErrors(['session' => 'Sesi Anda telah berakhir karena tidak aktif lebih dari 1 jam. Silakan login kembali.']);
            }

            // Perbarui waktu aktivitas terakhir setiap request
            session(['last_activity_time' => time()]);
        } else {
            // User belum login, bersihkan sisa data waktu jika ada
            $request->session()->forget('last_activity_time');
        }

        return $next($request);
    }
}
