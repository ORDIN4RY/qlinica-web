<?php

namespace App\Http\Controllers;

use App\Models\RekamMedis;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function penanganan(Request $request)
    {
        $query = RekamMedis::with(['pasien', 'dokter', 'diagnosa.icdx', 'resep.details.obat']);

        // 1. FILTER WAKTU (Periode / Tanggal)
        $periode = $request->get('periode');
        $tgl_awal = $request->get('tgl_awal');
        $tgl_akhir = $request->get('tgl_akhir');

        if ($periode) {
            $now = Carbon::now();
            switch ($periode) {
                case 'hari':
                    $query->whereDate('tanggal_periksa', $now->toDateString());
                    break;
                case 'minggu':
                    $query->whereBetween('tanggal_periksa', [$now->startOfWeek()->toDateString(), $now->endOfWeek()->toDateString()]);
                    break;
                case 'bulan':
                    $query->whereMonth('tanggal_periksa', $now->month)
                          ->whereYear('tanggal_periksa', $now->year);
                    break;
                case 'tahun':
                    $query->whereYear('tanggal_periksa', $now->year);
                    break;
                case 'custom':
                    if ($tgl_awal && $tgl_akhir) {
                        $query->whereBetween('tanggal_periksa', [$tgl_awal, $tgl_akhir]);
                    } elseif ($tgl_awal) {
                        $query->whereDate('tanggal_periksa', '>=', $tgl_awal);
                    } elseif ($tgl_akhir) {
                        $query->whereDate('tanggal_periksa', '<=', $tgl_akhir);
                    }
                    break;
            }
        }

        // 2. FILTER DOKTER
        if ($request->filled('dokter_id')) {
            $query->where('dokter_id', $request->get('dokter_id'));
        }

        // 3. FILTER KASUS PENYAKIT
        if ($request->filled('kasus_penyakit')) {
            $query->where('kasus_penyakit', $request->get('kasus_penyakit'));
        }

        // 4. FILTER STATUS PASIEN (Keadaan Keluar)
        if ($request->filled('keadaan_keluar')) {
            $query->where('keadaan_keluar', $request->get('keadaan_keluar'));
        }

        // 5. PENCARIAN (RM / Nama Pasien)
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('pasien', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('no_rm', 'like', "%{$search}%");
            });
        }

        // Ambil data yang sudah terurut
        $laporans = $query->orderBy('tanggal_periksa', 'desc')->paginate(15)->withQueryString();

        // Data untuk Dropdown Filter
        $dokters = Pegawai::whereHas('user', function($q) {
            $q->where('role', 'dokter');
        })->get();

        $keadaan_keluars = RekamMedis::select('keadaan_keluar')
            ->whereNotNull('keadaan_keluar')
            ->where('keadaan_keluar', '!=', '')
            ->distinct()
            ->pluck('keadaan_keluar');

        return view('admin.laporan_penanganan', compact('laporans', 'dokters', 'keadaan_keluars'));
    }
}
