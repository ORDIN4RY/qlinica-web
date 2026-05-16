<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MobileAuthController extends Controller
{
    /**
     * Login pegawai dari aplikasi mobile.
     * Menggunakan email + password, return Sanctum token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.',
            ], 401);
        }

        if ($user->role === 'pasien') {
            return response()->json([
                'success' => false,
                'message' => 'Akun pasien tidak dapat login di aplikasi ini.',
            ], 403);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Hubungi admin.',
            ], 403);
        }

        // Hapus token lama (opsional: bisa dipertahankan untuk multi-device)
        $user->tokens()->where('name', 'mobile')->delete();

        $token = $user->createToken('mobile')->plainTextToken;

        // Ambil data pegawai jika ada
        $pegawai = Pegawai::with('jabatan')->where('user_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'token'   => $token,
            'user'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'role'     => $user->role,
                'foto'     => $user->foto,
                'pegawai'  => $pegawai ? [
                    'id'          => $pegawai->id,
                    'nik'         => $pegawai->nik,
                    'nama'        => $pegawai->nama,
                    'jabatan'     => $pegawai->jabatan?->nama ?? '-',
                    'spesialisasi'=> $pegawai->spesialisasi,
                    'no_hp'       => $pegawai->no_hp,
                ] : null,
            ],
        ]);
    }

    /**
     * Logout: hapus token aktif.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    /**
     * Ambil profil user yang sedang login.
     */
    public function me(Request $request)
    {
        $user    = $request->user();
        $pegawai = Pegawai::with('jabatan')->where('user_id', $user->id)->first();

        return response()->json([
            'success' => true,
            'user'    => [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'role'    => $user->role,
                'foto'    => $user->foto,
                'pegawai' => $pegawai ? [
                    'id'          => $pegawai->id,
                    'nik'         => $pegawai->nik,
                    'nama'        => $pegawai->nama,
                    'jabatan'     => $pegawai->jabatan?->nama ?? '-',
                    'spesialisasi'=> $pegawai->spesialisasi,
                    'no_hp'       => $pegawai->no_hp,
                ] : null,
            ],
        ]);
    }
}
