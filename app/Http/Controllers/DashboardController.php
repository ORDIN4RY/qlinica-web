<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalPatients = Patient::count();

        $todayVisits = Patient::whereDate('visit_date', today())->count();

        $totalDiseases = Patient::distinct('disease')->count('disease');

        // Top diseases for donut chart
        $diseaseData = Patient::select('disease', DB::raw('count(*) as total'))
            ->groupBy('disease')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // Monthly visits for current year (line chart)
        $year = Carbon::now()->year;
        $monthlyRaw = Patient::selectRaw('MONTH(visit_date) as month, COUNT(*) as total')
            ->whereYear('visit_date', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthlyVisits = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthlyVisits[] = $monthlyRaw->get($m, 0);
        }

        $recentPatients = Patient::latest()->limit(10)->get();

        // CRUD Pasien
        $search = $request->input('search');
        $patients = Patient::when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('disease', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        return view('beranda_admin', compact(
            'totalPatients',
            'todayVisits',
            'totalDiseases',
            'diseaseData',
            'monthlyVisits',
            'year',
            'recentPatients',
            'patients',
            'search'
        ));
    }
}
