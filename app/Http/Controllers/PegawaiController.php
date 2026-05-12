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
    /** Halaman utama data pegawai (server-side render). */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pegawais = Pegawai::with('user')
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik',  'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pegawai', compact('pegawais', 'search'));
    }

    /** Simpan pegawai baru. */
    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:100',
            'email'        => 'required|email|max:100|unique:users,email',
            'role'         => ['required', Rule::in(['admin', 'dokter', 'perawat', 'apoteker'])],
            'password'     => 'required|string|min:6',
            'nik'          => 'required|string|max:20|unique:pegawai,nik',
            'no_hp'        => 'required|string|max:15',
            'alamat'       => 'nullable|string',
            'spesialisasi' => 'nullable|string|max:100',
            'no_sip'       => 'required_if:role,dokter,perawat,apoteker|nullable|string|max:60',
        ], [
            'nama.required'     => 'Nama wajib diisi.',
            'email.required'    => 'Email wajib diisi.',
            'email.unique'      => 'Email sudah terdaftar.',
            'no_sip.required_if'=> 'Nomor SIP wajib diisi untuk role dokter, perawat, atau apoteker.',
            'no_hp.required'    => 'Nomor HP wajib diisi.',
            'nik.required'      => 'NIK wajib diisi.',
            'role.required'     => 'Role wajib dipilih.',
            'password.required' => 'Password wajib diisi.',
            'password.min'      => 'Password minimal 6 karakter.',
            'nik.unique'        => 'NIK sudah terdaftar.',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'      => $request->nama,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'role'      => $request->role,
                'phone'     => $request->no_hp,
                'is_active' => true,
            ]);

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

        return redirect()->route('admin.pegawai')
            ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    /** Update data pegawai. */
    public function update(Request $request, $id)
    {
        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'nama'         => 'required|string|max:100',
            'email'        => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($pegawai->user_id)],
            'role'         => ['required', Rule::in(['admin', 'dokter', 'perawat', 'apoteker'])],
            'nik'          => ['required', 'string', 'max:20', Rule::unique('pegawai', 'nik')->ignore($id)],
            'no_hp'        => 'required|string|max:15',
            'alamat'       => 'nullable|string',
            'spesialisasi' => 'nullable|string|max:100',
            'no_sip'       => 'required_if:role,dokter,perawat,apoteker|nullable|string|max:60',
            'password'     => 'nullable|string|min:6',
        ], [
            'nama.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah digunakan.',
            'nik.required'       => 'NIK wajib diisi.',
            'nik.unique'         => 'NIK sudah terdaftar.',
            'no_hp.required'     => 'Nomor HP wajib diisi.',
            'no_sip.required_if' => 'Nomor SIP wajib diisi untuk role dokter, perawat, atau apoteker.',
            'password.min'       => 'Password minimal 6 karakter.',
        ]);

        DB::transaction(function () use ($request, $pegawai) {
            if ($pegawai->user) {
                $pegawai->user->update([
                    'name'  => $request->nama,
                    'email' => $request->email,
                    'role'  => $request->role,
                    'phone' => $request->no_hp,
                ]);

                // Update password via DB langsung agar tidak double-hash
                // karena User model sudah punya cast 'hashed'
                if ($request->filled('password')) {
                    DB::table('users')
                        ->where('id', $pegawai->user->id)
                        ->update(['password' => Hash::make($request->password)]);
                }
            }

            $pegawai->update([
                'nik'          => $request->nik,
                'nama'         => $request->nama,
                'spesialisasi' => $request->spesialisasi,
                'no_sip'       => $request->no_sip,
                'alamat'       => $request->alamat,
                'no_hp'        => $request->no_hp,
            ]);
        });

        return redirect()->route('admin.pegawai')
            ->with('success', 'Data pegawai berhasil diperbarui.');
    }

    /** Hapus pegawai (soft delete) + nonaktifkan user. */
    public function destroy($id)
    {
        $pegawai = Pegawai::findOrFail($id);

        DB::transaction(function () use ($pegawai) {
            if ($pegawai->user) {
                $pegawai->user->update(['is_active' => false]);
            }
            $pegawai->delete();
        });

        return redirect()->route('admin.pegawai')
            ->with('success', 'Pegawai berhasil dihapus.');
    }

    /** Search pegawai untuk autocomplete. */
    public function search(Request $request)
    {
        $q = $request->input('q');
        $pegawais = Pegawai::where('nama', 'like', "%{$q}%")
            ->orWhere('nik', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'nama', 'nik']);

        return response()->json($pegawais);
    }
}
