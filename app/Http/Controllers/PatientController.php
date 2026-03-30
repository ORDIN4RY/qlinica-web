<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $patients = Patient::when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('nik', 'like', "%{$search}%")
              ->orWhere('disease', 'like', "%{$search}%");
        })->latest()->paginate(10)->withQueryString();

        return view('patients.index', compact('patients', 'search'));
    }

    public function create()
    {
        return view('patients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'nik'        => 'required|string|size:16|unique:patients,nik',
            'age'        => 'required|integer|min:0|max:150',
            'gender'     => 'required|in:L,P',
            'address'    => 'required|string',
            'phone'      => 'required|string|max:20',
            'disease'    => 'required|string|max:255',
            'visit_date' => 'required|date',
            'notes'      => 'nullable|string',
        ]);

        Patient::create($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Pasien berhasil ditambahkan.');
    }

    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'nik'        => 'required|string|size:16|unique:patients,nik,' . $patient->id,
            'age'        => 'required|integer|min:0|max:150',
            'gender'     => 'required|in:L,P',
            'address'    => 'required|string',
            'phone'      => 'required|string|max:20',
            'disease'    => 'required|string|max:255',
            'visit_date' => 'required|date',
            'notes'      => 'nullable|string',
        ]);

        $patient->update($validated);

        return redirect()->route('patients.index')
            ->with('success', 'Data pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')
            ->with('success', 'Pasien berhasil dihapus.');
    }
}
