<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect('/');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            // Jika role tidak sesuai, buang ke halamannya masing-masing
            if ($user->role === 'admin') {
                return redirect()->route('beranda_admin');
            } elseif ($user->role === 'pasien') {
                return redirect()->route('pasien.portal');
            }
            
            return redirect('/');
        }

        return $next($request);
    }
}
