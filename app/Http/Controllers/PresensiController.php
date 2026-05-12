<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PresensiController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        
        $presensis = Presensi::with('pegawai')
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->orderBy('tanggal', 'desc')
            ->get();
            
        $pegawais = Pegawai::all();
        
        // KPI Summary
        $kpi = [
            'hadir' => $presensis->where('status', 'Hadir')->count(),
            'sakit' => $presensis->where('status', 'Sakit')->count(),
            'izin' => $presensis->where('status', 'Izin')->count(),
            'cuti' => $presensis->where('status', 'Cuti')->count(),
            'alpa' => $presensis->where('status', 'Alpa')->count(),
        ];

        return view('admin.presensi', compact('presensis', 'pegawais', 'bulan', 'tahun', 'kpi'));
    }

    public function update(Request $request, $id)
    {
        $presensi = Presensi::findOrFail($id);
        
        $request->validate([
            'approval_status' => 'required|in:Approved,Rejected',
        ]);

        $presensi->update([
            'approval_status' => $request->approval_status
        ]);

        return redirect()->route('admin.presensi')->with('success', 'Status pengajuan presensi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $presensi = Presensi::findOrFail($id);
        $presensi->delete();

        return redirect()->route('admin.presensi')->with('success', 'Data presensi berhasil dihapus.');
    }
}
