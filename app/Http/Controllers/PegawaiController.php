<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PegawaiController extends Controller
{
    /** Tampilkan halaman manajemen pegawai. */
    public function index()
    {
        $pegawais = Pegawai::with('user')->get();
        return view('pegawai', compact('pegawais'));
    }

    /** Simpan petugas baru: insert ke users + pegawai. */
    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'email'  => 'required|email|max:100|unique:users,email',
            'role'   => ['required', Rule::in(['admin', 'dokter', 'perawat', 'apoteker'])],
            'password' => 'required|string|min:6',
            'nik'    => 'nullable|string|max:20|unique:pegawai,nik',
            'no_hp'  => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'spesialisasi' => 'nullable|string|max:100',
            'no_sip' => 'nullable|string|max:60',
        ], [
            'nama.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'role.required'     => 'Role wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'nik.unique'        => 'NIK sudah terdaftar.',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Buat akun di tabel users
            $user = User::create([
                'name'     => $request->nama,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
                'phone'    => $request->no_hp,
                'is_active'=> true,
            ]);

            // 2. Insert ke tabel pegawai
            Pegawai::create([
                'user_id'      => $user->id,
                'nik'          => $request->nik,
                'nama'         => $request->nama,
                'spesialisasi' => $request->spesialisasi,
                'no_sip'       => $request->no_sip,
                'alamat'       => $request->alamat,
                'no_hp'        => $request->no_hp,
            ]);
        });

        return response()->json(['message' => 'Petugas berhasil ditambahkan.']);
    }

    /** Tampilkan data pegawai (untuk AJAX). */
    public function fetchAll()
    {
        $pegawais = Pegawai::with('user')->get()->map(function ($p) {
            return [
                'id'           => $p->id,
                'user_id'      => $p->user_id,
                'nik'          => $p->nik ?? '-',
                'nama'         => $p->nama,
                'email'        => $p->user->email ?? '-',
                'role'         => $p->user->role ?? '-',
                'no_hp'        => $p->no_hp ?? '-',
                'alamat'       => $p->alamat ?? '-',
                'spesialisasi' => $p->spesialisasi ?? '-',
                'is_active'    => $p->user->is_active ?? true,
                'updated_at'   => $p->updated_at ? $p->updated_at->format('d-m-Y H:i') : '-',
            ];
        });

        return response()->json($pegawais);
    }

    /** Update data pegawai. */
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'nama'   => 'required|string|max:100',
            'email'  => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($pegawai->user_id)],
            'role'   => ['required', Rule::in(['admin', 'dokter', 'perawat', 'apoteker'])],
            'nik'    => ['nullable', 'string', 'max:20', Rule::unique('pegawai', 'nik')->ignore($id)],
            'no_hp'  => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'spesialisasi' => 'nullable|string|max:100',
            'no_sip' => 'nullable|string|max:60',
            'password' => 'nullable|string|min:6',
        ], [
            'nama.required'  => 'Nama wajib diisi.',
            'email.unique'   => 'Email sudah digunakan.',
            'nik.unique'     => 'NIK sudah terdaftar.',
            'password.min'   => 'Password minimal 6 karakter.',
        ]);

        DB::transaction(function () use ($request, $pegawai) {
            // Update users
            $userData = [
                'name'  => $request->nama,
                'email' => $request->email,
                'role'  => $request->role,
                'phone' => $request->no_hp,
            ];
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $pegawai->user->update($userData);

            // Update pegawai
            $pegawai->update([
                'nik'          => $request->nik,
                'nama'         => $request->nama,
                'spesialisasi' => $request->spesialisasi,
                'no_sip'       => $request->no_sip,
                'alamat'       => $request->alamat,
                'no_hp'        => $request->no_hp,
            ]);
        });

        return response()->json(['message' => 'Data pegawai berhasil diperbarui.']);
    }

    /** Hapus pegawai (soft delete) + nonaktifkan user. */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        DB::transaction(function () use ($pegawai) {
            $pegawai->user->update(['is_active' => false]);
            $pegawai->delete();
        });

        return response()->json(['message' => 'Pegawai berhasil dihapus.']);
    }
}
