<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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
     * Kirim OTP untuk reset password.
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Email tidak terdaftar.'
        ]);

        $otp = sprintf("%06d", mt_rand(1, 999999));

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => $otp,
                'created_at' => Carbon::now()
            ]
        );

        try {
            Mail::raw("Kode OTP untuk reset password Anda adalah: {$otp}\n\nBerlaku selama 60 menit.", function ($message) use ($request) {
                $message->to($request->email)
                        ->subject('Kode OTP Reset Password');
            });
        } catch (\Exception $e) {
            \Log::error('Gagal mengirim email OTP: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP telah dikirim ke email Anda.',
        ]);
    }

    /**
     * Verifikasi OTP sebelum reset password.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'token'    => 'required|string',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid.'
            ], 400);
        }

        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kedaluwarsa.'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP valid.',
        ]);
    }

    /**
     * Reset password menggunakan OTP.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email|exists:users,email',
            'token'    => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$reset) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP tidak valid.'
            ], 400);
        }

        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP sudah kedaluwarsa.'
            ], 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil direset. Silakan login dengan password baru.',
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
     * Update foto profil user dari mobile.
     */
    public function updateFoto(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo if it exists and is not the default
            if ($user->foto && \Storage::disk('public')->exists($user->foto)) {
                \Storage::disk('public')->delete($user->foto);
            }

            // Store new photo
            $path = $request->file('foto')->store('profil', 'public');

            // Update user
            $user->update([
                'foto' => $path,
            ]);

            // Update pegawai if exists
            if ($user->pegawai) {
                $user->pegawai->update([
                    'foto' => $path,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui.',
                'user'    => $this->me($request)->original['user'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada file gambar yang diunggah.',
        ], 400);
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
