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
                    'jabatan'     => $pegawai->jabatan?->nama_jabatan ?? '-',
                    'spesialisasi'=> $pegawai->spesialisasi,
                    'no_hp'       => $pegawai->no_hp,
                    'alamat'      => $pegawai->alamat,
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
                    'jabatan'     => $pegawai->jabatan?->nama_jabatan ?? '-',
                    'spesialisasi'=> $pegawai->spesialisasi,
                    'no_hp'       => $pegawai->no_hp,
                    'alamat'      => $pegawai->alamat,
                ] : null,
            ],
        ]);
    }

    /**
     * Update profil user dari mobile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        // Update User
        $user->update([
            'name' => $request->name,
        ]);

        // Update Pegawai
        if ($user->pegawai) {
            $user->pegawai->update([
                'nama'  => $request->name,
                'no_hp' => $request->phone,
                'alamat'=> $request->alamat,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $this->me($request)->original['user'],
        ]);
    }

    /**
     * Ganti password user yang sedang login.
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'new_password'          => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password lama tidak sesuai.',
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
