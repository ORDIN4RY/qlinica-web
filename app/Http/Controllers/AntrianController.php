<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Antrian;
use App\Models\Pegawai;
use App\Models\RekamMedis;

class AntrianController extends Controller
{
    public function index()
    {
        $antrians = Antrian::with('pasien')
            ->where('tanggal', now()->toDateString())
            ->orderBy('no_antrian')
            ->get();

        $dokters = Pegawai::whereHas('user', function($q) {
            $q->where('jabatan_id', '2');
        })->get();

        return view('admin.pemesanan', [
            'antrians' => $antrians,
            'dokters' => $dokters,
            'jumlahAntrian' => $antrians->count(),
            'terpanggil' => $antrians->where('status', 'Dipanggil')->count(),
            'selesai' => $antrians->where('status', 'Selesai')->count(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pasien_id' => 'required|exists:pasien,id',
            'jenis_pemesan' => 'required|in:Offline',
            'status' => 'required|in:Menunggu',
            'catatan' => 'nullable|string|max:255',
        ]);

        $lastAntrian = Antrian::where('tanggal', now()->toDateString())
            ->orderBy('no_antrian', 'desc')
            ->first();

        $nextNo = $lastAntrian ? $lastAntrian->no_antrian + 1 : 1;

        Antrian::create([
            'no_antrian' => $nextNo,
            'pasien_id' => $request->pasien_id,
            'tanggal' => now()->toDateString(),
            'jenis' => $request->jenis_pemesan,
            'keluhan' => $request->catatan,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.pemesanan')->with('success', 'Antrian berhasil ditambahkan.');
    }

    public function panggilPeriksa(Request $request, $id)
    {
        $antrian = Antrian::findOrFail($id);

        $request->validate([
            'dokter_id' => 'required|exists:pegawai,id',
            'tekanan_darah' => 'required|string|max:20',
            'suhu' => 'required|numeric|min:30|max:45',
            'berat_badan' => 'required|numeric|min:1|max:200',
            'tinggi_badan' => 'required|numeric|min:30|max:250',
            'nadi' => 'required|integer|min:40|max:200',
            'respirasi' => 'required|integer|min:10|max:60',
        ]);


        DB::transaction(function () use ($antrian, $request) {
            $antrian->update(['status' => 'Dipanggil']);

            RekamMedis::create([
                'antrian_id' => $antrian->id,
                'pasien_id' => $antrian->pasien_id,
                'dokter_id' => $request->dokter_id,
                'tanggal_periksa' => now(),
                'tekanan_darah' => $request->tekanan_darah,
                'suhu' => $request->suhu,
                'berat_badan' => $request->berat_badan,
                'tinggi_badan' => $request->tinggi_badan,
                'nadi' => $request->nadi,
                'respirasi' => $request->respirasi,
            ]);
        });

        return redirect()->route('admin.pemesanan')->with('success', 'Pasien berhasil dipanggil dan TTV telah disimpan.');
    }

    /**
     * Simpan antrian online dari portal pasien.
     * Dipanggil via AJAX (fetch) dari dashboard_pasien.blade.php.
     */
    public function storePasien(Request $request)
    {
        // Pastikan pasien sudah login dan memiliki data pasien
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Data pasien tidak ditemukan. Hubungi admin.',
            ], 422);
        }

        $request->validate([
            'jenis'   => 'required|string|max:100',
            'keluhan' => 'nullable|string|max:1000',
        ]);

        // Cegah duplikat antrian aktif pada hari yang sama
        $sudahAda = Antrian::where('pasien_id', $pasien->id)
            ->where('tanggal', now()->toDateString())
            ->whereIn('status', ['Menunggu', 'Dipanggil'])
            ->whereNull('deleted_at')
            ->exists();

        if ($sudahAda) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah memiliki antrian aktif hari ini.',
            ], 422);
        }

        // Hitung nomor antrian berikutnya untuk hari ini
        $lastAntrian = Antrian::where('tanggal', now()->toDateString())
            ->whereNull('deleted_at')
            ->orderBy('no_antrian', 'desc')
            ->first();

        $nextNo = $lastAntrian ? $lastAntrian->no_antrian + 1 : 1;

        $antrian = Antrian::create([
            'no_antrian' => $nextNo,
            'pasien_id'  => $pasien->id,
            'jenis'      => 'Online',
            'keluhan'    => $request->keluhan ?: null,
            'status'     => 'Menunggu',
            'tanggal'    => now()->toDateString(),
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Antrian berhasil diambil!',
            'id'         => $antrian->id,
            'no_antrian' => str_pad($antrian->no_antrian, 3, '0', STR_PAD_LEFT),
            'layanan'    => $request->jenis,
            'tanggal'    => $antrian->tanggal->format('d M Y'),
        ]);
    }

    /**
     * Batalkan dan hapus permanen antrian online milik pasien.
     * Hanya bisa dibatalkan jika status masih 'Menunggu'.
     */
    public function cancelPasien(Request $request, $id)
    {
        $user   = auth()->user();
        $pasien = $user->pasien ?? null;

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Data pasien tidak ditemukan.',
            ], 422);
        }

        // Gunakan withTrashed agar bisa menemukan record soft-deleted jika ada
        $antrian = Antrian::withTrashed()
            ->where('id', $id)
            ->where('pasien_id', $pasien->id)
            ->first();

        if (!$antrian) {
            return response()->json([
                'success' => false,
                'message' => 'Antrian tidak ditemukan.',
            ], 404);
        }

        // Gunakan getRawOriginal agar tidak terpengaruh Eloquent accessor
        $statusRaw = $antrian->getRawOriginal('status');
        if (!in_array($statusRaw, ['Menunggu'])) {
            return response()->json([
                'success' => false,
                'message' => 'Antrian sudah diproses dan tidak bisa dibatalkan.',
            ], 422);
        }

        // Hard delete: hapus permanen dari tabel
        $antrian->forceDelete();

        return response()->json([
            'success' => true,
            'message' => 'Antrian berhasil dibatalkan.',
        ]);
    }

    // Placeholder untuk update status
    public function updateStatus(Request $request, $id)
    {
        $antrian = Antrian::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Menunggu,Terpanggil,Dipanggil,Dilayani,Selesai,Batal',
        ]);

        $antrian->update(['status' => $request->status]);

        $message = 'Status antrian berhasil diupdate.';
        return redirect()->route('admin.pemesanan')->with('success', $message);
    }
}