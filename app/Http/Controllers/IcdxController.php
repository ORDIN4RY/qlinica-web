<?php

namespace App\Http\Controllers;

use App\Models\Icdx;
use Illuminate\Http\Request;

class IcdxController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $perPage = $request->get('per_page', 15);

        $query = Icdx::query()->orderBy('kd_icdx');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kd_icdx', 'like', "%{$search}%")
                  ->orWhere('nama_icdx', 'like', "%{$search}%");
            });
        }

        $icdxs = $query->paginate($perPage)->withQueryString();

        return view('admin.icdx', compact('icdxs', 'search', 'perPage'));
    }
}
 