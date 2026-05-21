<?php

namespace App\Http\Controllers;

use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $query = Kamar::query();

        if ($request->filled('search')) {
            $query->where('nama_kamar', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_kamar', 'like', '%' . $request->search . '%');
        }

        $kamars = $query->orderBy('kelas')->orderBy('nama_kamar')->paginate(15);

        return view('admin.kamar', compact('kamars'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kamar' => 'required|string|max:20|unique:kamar',
            'nama_kamar' => 'required|string|max:100',
            'kelas' => 'required|in:VIP,Kelas 1,Kelas 2,Kelas 3',
            'tarif_per_malam' => 'required|numeric|min:0',
            'status' => 'required|in:Tersedia,Terisi,Perbaikan',
            'kapasitas' => 'required|integer|min:1',
            'terisi' => 'nullable|integer|min:0',
        ]);

        Kamar::create($validated);

        return redirect()->back()->with('success', 'Data kamar berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kamar = Kamar::findOrFail($id);

        $validated = $request->validate([
            'kode_kamar' => 'required|string|max:20|unique:kamar,kode_kamar,' . $id,
            'nama_kamar' => 'required|string|max:100',
            'kelas' => 'required|in:VIP,Kelas 1,Kelas 2,Kelas 3',
            'tarif_per_malam' => 'required|numeric|min:0',
            'status' => 'required|in:Tersedia,Terisi,Perbaikan',
            'kapasitas' => 'required|integer|min:1',
            'terisi' => 'nullable|integer|min:0',
        ]);

        $kamar->update($validated);

        return redirect()->back()->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kamar = Kamar::findOrFail($id);
        if ($kamar->status === 'Terisi') {
            return redirect()->back()->with('error', 'Kamar tidak dapat dihapus karena sedang terisi pasien.');
        }
        $kamar->delete();

        return redirect()->back()->with('success', 'Data kamar berhasil dihapus.');
    }
}
