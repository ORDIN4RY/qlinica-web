<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MobilePresensiController extends Controller
{
    /**
     * Ambil riwayat presensi pegawai yang login.
     * Query param: bulan (1-12), tahun (YYYY)
     */
    public function index(Request $request)
    {
        $user    = $request->user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Data pegawai tidak ditemukan.',
            ], 404);
        }

        $bulan = $request->get('bulan', now()->month);
        $tahun = $request->get('tahun', now()->year);

        $presensis = Presensi::where('pegawai_id', $pegawai->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get()
            ->map(fn($p) => $this->formatPresensi($p));

        // Cek apakah hari ini sudah clock-in / clock-out
        $today = Presensi::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', now()->toDateString())
            ->first();

        return response()->json([
            'success'     => true,
            'presensi'    => $presensis,
            'today'       => $today ? $this->formatPresensi($today) : null,
            'has_clocked_in'  => $today?->jam_masuk !== null,
            'has_clocked_out' => $today?->jam_keluar !== null,
        ]);
    }

    /**
     * Clock In: catat jam masuk.
     */
    public function clockIn(Request $request)
    {
        $request->validate([
            'latitude'          => 'required|numeric',
            'longitude'         => 'required|numeric',
            'is_location_valid' => 'required|boolean',
            'foto'              => 'nullable|string', // base64 atau path
        ]);

        $user    = $request->user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        $today = now()->toDateString();

        // Cek sudah clock-in hari ini
        $existing = Presensi::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existing && $existing->jam_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen masuk hari ini.',
            ], 409);
        }

        $presensi = Presensi::updateOrCreate(
            ['pegawai_id' => $pegawai->id, 'tanggal' => $today],
            [
                'jam_masuk'         => now()->toTimeString(),
                'status'            => 'Hadir',
                'approval_status'   => 'Pending',
                'keterangan'        => $request->is_location_valid
                    ? 'Absen dalam radius kantor'
                    : 'Absen di luar radius kantor',
            ]
        );

        return response()->json([
            'success'  => true,
            'message'  => 'Absen masuk berhasil dicatat.',
            'presensi' => $this->formatPresensi($presensi),
        ]);
    }

    /**
     * Clock Out: catat jam keluar.
     */
    public function clockOut(Request $request)
    {
        $request->validate([
            'latitude'          => 'required|numeric',
            'longitude'         => 'required|numeric',
            'is_location_valid' => 'required|boolean',
        ]);

        $user    = $request->user();
        $pegawai = Pegawai::where('user_id', $user->id)->first();

        if (!$pegawai) {
            return response()->json(['success' => false, 'message' => 'Data pegawai tidak ditemukan.'], 404);
        }

        $today    = now()->toDateString();
        $presensi = Presensi::where('pegawai_id', $pegawai->id)
            ->whereDate('tanggal', $today)
            ->first();

        if (!$presensi || !$presensi->jam_masuk) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absen masuk hari ini.',
            ], 409);
        }

        if ($presensi->jam_keluar) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen pulang hari ini.',
            ], 409);
        }

        $presensi->update([
            'jam_keluar'      => now()->toTimeString(),
            'approval_status' => 'Pending',
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Absen pulang berhasil dicatat.',
            'presensi' => $this->formatPresensi($presensi->fresh()),
        ]);
    }

    /**
     * Helper: format data presensi untuk response JSON.
     */
    private function formatPresensi(Presensi $p): array
    {
        return [
            'id'              => $p->id,
            'tanggal'         => $p->tanggal,
            'jam_masuk'       => $p->jam_masuk,
            'jam_keluar'      => $p->jam_keluar,
            'status'          => $p->status,
            'approval_status' => $p->approval_status,
            'keterangan'      => $p->keterangan,
        ];
    }
}
