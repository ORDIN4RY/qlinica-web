<?php

namespace App\Http\Controllers;

use App\Models\Agama;
use App\Models\Pasien;
use App\Models\Pekerjaan;
use App\Models\Pendidikan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasienController extends Controller
{
    /** Halaman utama data pasien (server-side render). */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $pasiens = Pasien::with(['user', 'agama', 'pendidikan', 'pekerjaan'])
            ->when($search, function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik',   'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $agamas      = Agama::orderBy('agama')->get(['id', 'agama']);
        $pendidikans = Pendidikan::orderBy('pendidikan')->get(['id', 'pendidikan']);
        $pekerjaans  = Pekerjaan::orderBy('pekerjaan')->get(['id', 'pekerjaan']);

        return view('pasien', compact('pasiens', 'search', 'agamas', 'pendidikans', 'pekerjaans'));
    }

    /** Ambil semua data pasien (AJAX). */
    public function fetchAll()
    {
        $pasiens = Pasien::with(['user', 'agama', 'pendidikan', 'pekerjaan'])->get()->map(function ($p) {
            // Hitung umur dari tgl_lahir (tidak disimpan di kolom)
            $umur = $p->tgl_lahir ? Carbon::parse($p->tgl_lahir)->age : null;

            return [
                'id'              => $p->id,
                'no_rm'           => $p->no_rm,
                'nik'             => $p->nik ?? '',
                'nama'            => $p->nama,
                'nama_kk'         => $p->nama_kk ?? '',
                'tgl_lahir'       => $p->tgl_lahir ? $p->tgl_lahir->format('Y-m-d') : '',
                'umur'            => $umur,
                'jenis_kelamin'   => $p->jenis_kelamin,
                'golongan_darah'  => $p->golongan_darah ?? '',
                'alamat'          => $p->alamat ?? '',
                'desa'            => $p->desa ?? '',
                'kota'            => $p->kota ?? '',
                'no_hp'           => $p->user->phone ?? '',
                'agama_id'        => $p->agama_id,
                'agama_nama'      => $p->agama->agama ?? '',
                'pendidikan_id'   => $p->pendidikan_id,
                'pendidikan_nama' => $p->pendidikan->pendidikan ?? '',
                'pekerjaan_id'    => $p->pekerjaan_id,
                'pekerjaan_nama'  => $p->pekerjaan->pekerjaan ?? '',
                'updated_at'      => $p->updated_at ? $p->updated_at->format('d-m-Y H:i') : '-',
            ];
        });

        return response()->json($pasiens);
    }

    /** Simpan pasien baru. */
    public function store(Request $request)
    {
        $request->validate([
            'no_rm'          => 'required|string|max:15|unique:pasien,no_rm',
            'nama'           => 'required|string|max:100',
            'tgl_lahir'      => 'required|date',
            'jenis_kelamin'  => 'required|in:L,P',
            'nik'            => 'nullable|string|max:16|unique:pasien,nik',
            'nama_kk'        => 'nullable|string|max:100',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'alamat'         => 'nullable|string',
            'desa'           => 'nullable|string|max:50',
            'kota'           => 'nullable|string|max:50',
            'no_hp'          => 'nullable|string|max:15',
            'agama_id'       => 'nullable|integer',
            'pendidikan_id'  => 'nullable|integer',
            'pekerjaan_id'   => 'nullable|integer',
            'password'       => 'required|string|min:6',
        ], [
            'no_rm.required'  => 'No. Rekam Medik wajib diisi.',
            'no_rm.unique'    => 'No. Rekam Medik sudah terdaftar.',
            'nama.required'   => 'Nama wajib diisi.',
            'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'nik.unique'      => 'NIK sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min'    => 'Password minimal 6 karakter.',
        ]);

        DB::transaction(function () use ($request) {
            // Buat akun user untuk pasien
            $user = User::create([
                'name'      => $request->nama,
                'email'     => 'pasien_' . $request->no_rm . '@sahaduta.local',
                'password'  => Hash::make($request->password),
                'role'      => 'pasien',
                'phone'     => $request->no_hp,
                'is_active' => true,
            ]);

            Pasien::create([
                'user_id'        => $user->id,
                'no_rm'          => $request->no_rm,
                'nik'            => $request->nik,
                'nama'           => $request->nama,
                'nama_kk'        => $request->nama_kk,
                'tgl_lahir'      => $request->tgl_lahir,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'golongan_darah' => $request->golongan_darah ?: null,
                'alamat'         => $request->alamat,
                'desa'           => $request->desa,
                'kota'           => $request->kota,
                'agama_id'       => $request->agama_id ?: null,
                'pendidikan_id'  => $request->pendidikan_id ?: null,
                'pekerjaan_id'   => $request->pekerjaan_id ?: null,
            ]);
        });

        return redirect()->route('admin.pasien')
            ->with('success', 'Pasien berhasil ditambahkan.');
    }

    /** Update data pasien. */
    public function update(Request $request, $id)
    {
        $pasien = Pasien::findOrFail($id);

        $request->validate([
            'no_rm'          => 'required|string|max:15|unique:pasien,no_rm,' . $id,
            'nama'           => 'required|string|max:100',
            'tgl_lahir'      => 'required|date',
            'jenis_kelamin'  => 'required|in:L,P',
            'nik'            => 'nullable|string|max:16|unique:pasien,nik,' . $id,
            'nama_kk'        => 'nullable|string|max:100',
            'golongan_darah' => 'nullable|in:A,B,AB,O',
            'alamat'         => 'nullable|string',
            'desa'           => 'nullable|string|max:50',
            'kota'           => 'nullable|string|max:50',
            'no_hp'          => 'nullable|string|max:15',
            'agama_id'       => 'nullable|integer',
            'pendidikan_id'  => 'nullable|integer',
            'pekerjaan_id'   => 'nullable|integer',
            'password'       => 'nullable|string|min:6',
        ], [
            'no_rm.unique'   => 'No. Rekam Medik sudah digunakan.',
            'nik.unique'     => 'NIK sudah terdaftar.',
            'password.min'   => 'Password minimal 6 karakter.',
        ]);

        DB::transaction(function () use ($request, $pasien) {
            $pasien->update([
                'no_rm'          => $request->no_rm,
                'nik'            => $request->nik,
                'nama'           => $request->nama,
                'nama_kk'        => $request->nama_kk,
                'tgl_lahir'      => $request->tgl_lahir,
                'jenis_kelamin'  => $request->jenis_kelamin,
                'golongan_darah' => $request->golongan_darah ?: null,
                'alamat'         => $request->alamat,
                'desa'           => $request->desa,
                'kota'           => $request->kota,
                'agama_id'       => $request->agama_id ?: null,
                'pendidikan_id'  => $request->pendidikan_id ?: null,
                'pekerjaan_id'   => $request->pekerjaan_id ?: null,
            ]);

            if ($pasien->user) {
                $userData = [
                    'name'  => $request->nama,
                    'phone' => $request->no_hp,
                ];
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                $pasien->user->update($userData);
            }
        });

        return redirect()->route('admin.pasien')
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    /** Hapus pasien (soft delete). */
    public function destroy($id)
    {
        $pasien = Pasien::findOrFail($id);
        DB::transaction(function () use ($pasien) {
            if ($pasien->user) {
                $pasien->user->update(['is_active' => false]);
            }
            $pasien->delete();
        });

        return redirect()->route('admin.pasien')
            ->with('success', 'Pasien berhasil dihapus.');
    }
}
