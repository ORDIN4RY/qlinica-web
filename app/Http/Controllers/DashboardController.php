<?php

namespace App\Http\Controllers;

use App\Models\Pasien;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Total pasien terdaftar
        $totalPasien = Pasien::count();

        // Pasien baru hari ini (berdasarkan created_at)
        $todayNew = Pasien::whereDate('created_at', today())->count();

        // Distribusi jenis kelamin
        $lakiLaki   = Pasien::where('jenis_kelamin', 'L')->count();
        $perempuan  = Pasien::where('jenis_kelamin', 'P')->count();

        // Distribusi golongan darah
        $golDarah = Pasien::select('golongan_darah', DB::raw('count(*) as total'))
            ->whereNotNull('golongan_darah')
            ->groupBy('golongan_darah')
            ->orderByDesc('total')
            ->get();

        // Pendaftaran pasien per bulan tahun ini (line/bar chart)
        $year = Carbon::now()->year;
        $monthlyRaw = Pasien::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthlyData = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyData[] = $monthlyRaw->get($m, 0);
        }

        // Pasien terbaru
        $recentPasiens = Pasien::with(['user'])->latest()->limit(10)->get();

        // CRUD Pasien — search & paginate
        $search = $request->input('search');
        $Pasiens = Pasien::when($search, function ($q) use ($search) {
            $q->where('nama', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('no_rm', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        return view('beranda_admin', compact(
            'totalPasien',
            'todayNew',
            'lakiLaki',
            'perempuan',
            'golDarah',
            'monthlyData',
            'year',
            'recentPasiens',
            'Pasiens',
            'search'
        ));
    }
}
