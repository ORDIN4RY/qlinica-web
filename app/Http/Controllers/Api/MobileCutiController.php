<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class MobileCutiController extends Controller
{
    /**
     * Ambil daftar pengajuan cuti/izin/sakit yang sudah dikelompokkan per pengajuan.
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        // Ambil data mentah (hanya yang memiliki batch_id agar tidak double/bingung)
        $presensis = Presensi::where('pegawai_id', $pegawai->id)
            ->whereIn('status', ['Cuti', 'Izin', 'Sakit'])
            ->whereNotNull('batch_id')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();

        // Kelompokkan berdasarkan batch_id
        $grouped = $presensis->groupBy('batch_id');

        $result = [];
        foreach ($grouped as $batchId => $items) {
            $first = $items->last(); // Tanggal mulai
            $last  = $items->first(); // Tanggal selesai

            $result[] = [
                'batch_id'        => $batchId,
                'jenis'           => $first->status,
                'tanggal_mulai'   => $first->tanggal,
                'tanggal_selesai' => $last->tanggal,
                'durasi'          => $items->count(),
                'approval_status' => $first->approval_status,
                'keterangan'      => $first->keterangan,
                'surat_dokter'    => $first->surat_dokter ? asset('storage/' . $first->surat_dokter) : null,
                'created_at'      => $first->created_at?->toISOString(),
            ];
        }

        return response()->json([
            'success'   => true,
            'pengajuan' => $result,
        ]);
    }

    /**
     * Ambil info jatah cuti.
     */
    public function quota(Request $request)
    {
        $user    = $request->user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        $terpakai = Presensi::where('pegawai_id', $pegawai->id)
            ->where('status', 'Cuti')
            ->where('approval_status', 'Approved')
            ->whereYear('tanggal', now()->year)
            ->count();

        $sisa = (int) ($pegawai->jatah_cuti ?? 0);

        return response()->json([
            'success' => true,
            'quota'   => [
                'total'    => $sisa + $terpakai,
                'terpakai' => $terpakai,
                'sisa'     => $sisa,
            ]
        ]);
    }

    /**
     * Buat pengajuan cuti/izin/sakit baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis'           => 'required|in:Cuti,Izin,Sakit',
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'required|string|max:500',
            'surat_dokter'    => 'nullable|image|max:2048',
        ]);

        $user    = $request->user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        if ($request->jenis === 'Sakit' && !$request->hasFile('surat_dokter')) {
            return response()->json(['success' => false, 'message' => 'Pengajuan sakit wajib melampirkan surat dokter.'], 422);
        }

        $mulai   = Carbon::parse($request->tanggal_mulai);
        $selesai = Carbon::parse($request->tanggal_selesai);
        $days    = CarbonPeriod::create($mulai, $selesai)->count();

        if ($request->jenis === 'Cuti') {
            $terpakai = Presensi::where('pegawai_id', $pegawai->id)
                ->where('status', 'Cuti')
                ->where('approval_status', 'Approved')
                ->whereYear('tanggal', now()->year)
                ->count();
            
            $sisa = (int) ($pegawai->jatah_cuti ?? 0);

            if ($days > $sisa) {
                return response()->json(['success' => false, 'message' => "Jatah cuti tidak mencukupi. Sisa jatah: $sisa hari."], 422);
            }
        }

        $path = null;
        if ($request->hasFile('surat_dokter')) {
            $path = $request->file('surat_dokter')->store('surat_dokter', 'public');
        }

        // Generate batch_id unik untuk pengajuan ini
        $batchId = Str::uuid()->toString();

        foreach (CarbonPeriod::create($mulai, $selesai) as $date) {
            $tanggal = $date->toDateString();

            if (Presensi::where('pegawai_id', $pegawai->id)->whereDate('tanggal', $tanggal)->exists()) {
                continue;
            }

            Presensi::create([
                'pegawai_id'      => $pegawai->id,
                'batch_id'        => $batchId,
                'tanggal'         => $tanggal,
                'status'          => $request->jenis,
                'approval_status' => 'Pending',
                'keterangan'      => $request->keterangan,
                'surat_dokter'    => $path,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Pengajuan berhasil dikirim.'], 201);
    }
}
