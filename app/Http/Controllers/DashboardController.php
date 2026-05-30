<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Icdx;
use App\Models\Pasien;
use App\Models\RekamMedis;
use App\Models\RekamMedisDiagnosa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Bypass khusus Admin (via tombol Cycle di sidebar)
        if ($user->role === 'admin') {
            if ($request->has('view')) {
                session(['bypass_view' => $request->query('view')]);
                return redirect()->route('beranda_admin');
            }

            $viewType = session('bypass_view', 'admin');
            if ($viewType === 'dokter') {
                return app(\App\Http\Controllers\DokterController::class)->dashboard();
            }
            if ($viewType === 'apoteker') {
                return app(\App\Http\Controllers\ObatController::class)->dashboard();
            }
            // Fallthrough ke blok admin jika viewType = admin
        } else {
            // 2. Render sesuai sub-akses (Tetap di rute /admin/dashboard)
            if (!$user->hasMenuAccess('Dashboard', 'admin_dashboard')) {
                if ($user->hasMenuAccess('Dashboard', 'dokter_dashboard')) {
                    return app(\App\Http\Controllers\DokterController::class)->dashboard();
                }
                if ($user->hasMenuAccess('Dashboard', 'apoteker_dashboard')) {
                    return app(\App\Http\Controllers\ObatController::class)->dashboard();
                }
                // Fallback
                return redirect()->route('admin.pasien');
            }
        }

        $year  = (int) $request->input('year', Carbon::now()->year);
        $month = (int) $request->input('month', Carbon::now()->month - 1); // 0-indexed untuk JS

        // ── KPI: Kunjungan per tahun ──────────────────────────────────
        $visitsByYear = Antrian::selectRaw("
                SUM(CASE WHEN p.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki,
                SUM(CASE WHEN p.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan,
                COUNT(*) as total
            ")
            ->join('pasien as p', 'p.id', '=', 'antrian.pasien_id')
            ->whereYear('antrian.tanggal', $year)
            ->whereNull('antrian.deleted_at')
            ->first();

        $totalTahun     = $visitsByYear->total ?? Pasien::count();
        $lakiTahun      = $visitsByYear->laki ?? Pasien::where('jenis_kelamin', 'L')->count();
        $perempuanTahun = $visitsByYear->perempuan ?? Pasien::where('jenis_kelamin', 'P')->count();

        // ── KPI: Kunjungan per bulan (12 bulan untuk chart) ──────────
        $monthlyRaw = Antrian::selectRaw("
                EXTRACT(MONTH FROM tanggal)::int as bulan,
                SUM(CASE WHEN p.jenis_kelamin = 'L' THEN 1 ELSE 0 END) as laki,
                SUM(CASE WHEN p.jenis_kelamin = 'P' THEN 1 ELSE 0 END) as perempuan
            ")
            ->join('pasien as p', 'p.id', '=', 'antrian.pasien_id')
            ->whereYear('antrian.tanggal', $year)
            ->whereNull('antrian.deleted_at')
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        // Selalu 12 slot
        $dataLaki      = [];
        $dataPerempuan = [];
        $bulanAdaData  = [];
        for ($m = 1; $m <= 12; $m++) {
            $row = $monthlyRaw->get($m);
            $l   = $row ? (int) $row->laki : 0;
            $p   = $row ? (int) $row->perempuan : 0;
            $dataLaki[]      = $l;
            $dataPerempuan[] = $p;
            if ($l + $p > 0) {
                $bulanAdaData[] = $m - 1; // 0-index untuk JS
            }
        }

        // ── Top 10 Penyakit ──────────────────────────────────────────
        $topPenyakit = RekamMedisDiagnosa::select('icdx_id', DB::raw('COUNT(*) as n'))
            ->groupBy('icdx_id')
            ->orderByDesc('n')
            ->limit(10)
            ->with('icdx')
            ->get()
            ->map(fn($d) => [
                'nama' => $d->icdx ? $d->icdx->nama : 'Tidak Diketahui',
                'n'    => $d->n,
            ])
            ->values();

        // Fallback: jika rekam_medis_diagnosa kosong, ambil dari icdx tanpa frekuensi
        if ($topPenyakit->isEmpty()) {
            $topPenyakit = Icdx::limit(10)->get()->map(fn($i) => [
                'nama' => $i->nama,
                'n'    => 0,
            ])->values();
        }

        // ── Distribusi Gender (Pasien terdaftar) ─────────────────────
        $lakiTotal      = Pasien::where('jenis_kelamin', 'L')->count();
        $perempuanTotal = Pasien::where('jenis_kelamin', 'P')->count();
        $totalPasien    = $lakiTotal + $perempuanTotal ?: 1;
        $lakiPct        = round($lakiTotal / $totalPasien * 100, 1);
        $perempuanPct   = round(100 - $lakiPct, 1);

        // ── Kepuasan Pasien (tabel feedback) ─────────────────────────
        $feedbackRatings = \App\Models\Feedback::whereYear('created_at', $year)
            ->whereMonth('created_at', $month + 1)
            ->select('penilaian', DB::raw('count(*) as total'))
            ->groupBy('penilaian')
            ->pluck('total', 'penilaian')
            ->toArray();

        $r5 = $feedbackRatings[5] ?? 0;
        $r4 = $feedbackRatings[4] ?? 0;
        $r3 = $feedbackRatings[3] ?? 0;
        $r2 = $feedbackRatings[2] ?? 0;
        $r1 = $feedbackRatings[1] ?? 0;

        $totalFeedback = $r5 + $r4 + $r3 + $r2 + $r1;

        if ($totalFeedback > 0) {
            $p5 = round(($r5 / $totalFeedback) * 100);
            $p4 = round(($r4 / $totalFeedback) * 100);
            $p3 = round(($r3 / $totalFeedback) * 100);
            $p2 = round(($r2 / $totalFeedback) * 100);
            $p1 = 100 - ($p5 + $p4 + $p3 + $p2); // adjust to sum to 100
            if ($p1 < 0) {
                $p1 = round(($r1 / $totalFeedback) * 100);
            }
        } else {
            $p5 = 0;
            $p4 = 0;
            $p3 = 0;
            $p2 = 0;
            $p1 = 0;
        }

        $kepuasanData = [$p5, $p4, $p3, $p2, $p1];


        // ── Tahun tersedia (untuk date-picker) ───────────────────────
        $tahunList = Antrian::selectRaw('EXTRACT(YEAR FROM tanggal)::int as tahun')
            ->whereNull('deleted_at')
            ->groupBy('tahun')
            ->orderByDesc('tahun')
            ->pluck('tahun')
            ->toArray();

        if (empty($tahunList)) {
            $tahunList = [Carbon::now()->year];
        }

        return view('admin.beranda_admin', compact(
            'year',
            'month',
            'totalTahun',
            'lakiTahun',
            'perempuanTahun',
            'dataLaki',
            'dataPerempuan',
            'bulanAdaData',
            'topPenyakit',
            'lakiTotal',
            'perempuanTotal',
            'lakiPct',
            'perempuanPct',
            'kepuasanData',
            'tahunList',
            'totalFeedback'
        ));
    }
}
