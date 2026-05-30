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
            switch ($periode) {
                case 'hari':
                    $query->whereDate('tanggal_periksa', Carbon::today()->toDateString());
                    break;
                case 'minggu':
                    $query->whereBetween('tanggal_periksa', [
                        Carbon::now()->startOfWeek()->toDateTimeString(),
                        Carbon::now()->endOfWeek()->toDateTimeString()
                    ]);
                    break;
                case 'bulan':
                    $query->whereMonth('tanggal_periksa', Carbon::now()->month)
                          ->whereYear('tanggal_periksa', Carbon::now()->year);
                    break;
                case 'tahun':
                    $query->whereYear('tanggal_periksa', Carbon::now()->year);
                    break;
                case 'custom':
                    if ($tgl_awal && $tgl_akhir) {
                        $query->whereBetween('tanggal_periksa', [$tgl_awal . ' 00:00:00', $tgl_akhir . ' 23:59:59']);
                    } elseif ($tgl_awal) {
                        $query->where('tanggal_periksa', '>=', $tgl_awal . ' 00:00:00');
                    } elseif ($tgl_akhir) {
                        $query->where('tanggal_periksa', '<=', $tgl_akhir . ' 23:59:59');
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

        // 5. PENCARIAN (RM, Nama Pasien, Dokter, ICDX, Tindakan, Pengobatan)
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('pasien', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%")
                      ->orWhere('no_rm', 'like', "%{$search}%");
                })
                ->orWhereHas('dokter', function ($q2) use ($search) {
                    $q2->where('nama', 'like', "%{$search}%");
                })
                ->orWhereHas('diagnosa.icdx', function ($q2) use ($search) {
                    $q2->where('kode', 'like', "%{$search}%")
                      ->orWhere('nama', 'like', "%{$search}%");
                })
                ->orWhere('tindakan', 'like', "%{$search}%")
                ->orWhere('pengobatan', 'like', "%{$search}%");
            });
        }

        // Ambil data yang sudah terurut
        $laporans = $query->orderBy('tanggal_periksa', 'desc')->paginate(15)->withQueryString();

        // Data untuk Dropdown Filter
        $dokters = Pegawai::whereHas('jabatan', function($q) {
            $q->where('nama_jabatan', 'Dokter');
        })->orWhereHas('user', function($q) {
            $q->where('role', 'dokter');
        })->get();

        // Menyediakan keadaan keluar standar untuk dropdown filter
        $keadaan_keluars = ['Sembuh', 'Membaik', 'Belum Sembuh', 'Meninggal'];

        return view('admin.laporan_penanganan', compact('laporans', 'dokters', 'keadaan_keluars'));
    }

    /**
     * Menyajikan Laporan Keuangan & Pembayaran Kasir.
     */
    public function keuangan(Request $request)
    {
        $query = \App\Models\Billing::with(['pasien', 'rekamMedis.dokter', 'kasir']);

        // 1. FILTER WAKTU (Periode / Tanggal)
        $periode = $request->get('periode', 'bulan'); // default bulan ini
        $tgl_awal = $request->get('tgl_awal');
        $tgl_akhir = $request->get('tgl_akhir');

        $now = Carbon::now();
        if ($periode) {
            switch ($periode) {
                case 'hari':
                    $query->whereDate('created_at', $now->toDateString());
                    break;
                case 'minggu':
                    $query->whereBetween('created_at', [$now->startOfWeek()->toDateTimeString(), $now->endOfWeek()->toDateTimeString()]);
                    break;
                case 'bulan':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
                case 'tahun':
                    $query->whereYear('created_at', $now->year);
                    break;
                case 'custom':
                    if ($tgl_awal && $tgl_akhir) {
                        $query->whereBetween('created_at', [$tgl_awal . ' 00:00:00', $tgl_akhir . ' 23:59:59']);
                    } elseif ($tgl_awal) {
                        $query->whereDate('created_at', '>=', $tgl_awal);
                    } elseif ($tgl_akhir) {
                        $query->whereDate('created_at', '<=', $tgl_akhir);
                    }
                    break;
            }
        }

        // 2. FILTER METODE PEMBAYARAN
        if ($request->filled('metode_pembayaran')) {
            $query->where('metode_pembayaran', $request->get('metode_pembayaran'));
        }

        // 3. FILTER STATUS
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Ambil semua data terfilter (tanpa pagination) untuk kalkulasi ringkasan
        $allFilteredBillings = $query->get();

        // 4. Kalkulasi Ringkasan Keuangan (Hanya dari yang berstatus 'Lunas')
        $lunasBillings = $allFilteredBillings->where('status', 'Lunas');
        
        $totalPendapatanKotor = $lunasBillings->sum('biaya_registrasi') + $lunasBillings->sum('biaya_tindakan') + $lunasBillings->sum('biaya_obat') + $lunasBillings->sum('biaya_kamar');
        $totalKlaimBpjs = $lunasBillings->sum('potongan_bpjs');
        $totalPendapatanBersih = $lunasBillings->sum('grand_total'); // Cash/EDC/QRIS yang masuk
        
        $totalBelumBayar = $allFilteredBillings->where('status', 'Belum Bayar')->sum('grand_total');

        // 5. Pendapatan per Metode Bayar
        $pendapatanMetode = [
            'Tunai' => $lunasBillings->where('metode_pembayaran', 'Tunai')->sum('grand_total'),
            'Asuransi' => $lunasBillings->where('metode_pembayaran', 'Asuransi')->sum('grand_total'),
        ];

        // 6. Data Grafik Harian
        $grafikData = $lunasBillings->groupBy(function($date) {
            return Carbon::parse($date->created_at)->format('d M Y');
        })->map(function($row) {
            return $row->sum('grand_total');
        });

        // Paginate data tabel untuk performa UI
        $billings = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('admin.laporan_keuangan', compact(
            'billings',
            'totalPendapatanKotor',
            'totalKlaimBpjs',
            'totalPendapatanBersih',
            'totalBelumBayar',
            'pendapatanMetode',
            'grafikData',
            'periode',
            'tgl_awal',
            'tgl_akhir'
        ));
    }
}
