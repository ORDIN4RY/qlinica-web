<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Antrian;

class AntrianController extends Controller
{
    public function index()
    {
        $antrians = Antrian::with('pasien')
            ->where('tanggal', now()->toDateString())
            ->orderBy('no_antrian')
            ->get();

        return view('admin.pemesanan', [
            'antrians' => $antrians,
            'jumlahAntrian' => $antrians->count(),
            'terpanggil' => $antrians->where('status', 'Dipanggil')->count(),
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
            'jadwal_dokter_id' => null,
            'tanggal' => now()->toDateString(),
            'jenis' => $request->jenis_pemesan,
            'keluhan' => $request->catatan,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.pemesanan')->with('success', 'Antrian berhasil ditambahkan.');
    }

    // Placeholder untuk update status
    public function updateStatus(Request $request, $id)
    {
        $antrian = Antrian::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Menunggu,Terpanggil,Dilayani,Selesai,Batal',
        ]);

        $antrian->update(['status' => $request->status]);

        $message = 'Status antrian berhasil diupdate.';
        return redirect()->route('admin.pemesanan')->with('success', $message);
    }
}