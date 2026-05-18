<?php

namespace App\Http\Controllers;

use App\Models\Obat;
use Illuminate\Http\Request;

class ObatController extends Controller
{
    public function index(Request $request)
    {
        $query = Obat::query();

        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('kode', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $obats     = $query->orderBy('nama')->paginate(15)->withQueryString();
        $kategoriList = Obat::select('kategori')->distinct()->whereNotNull('kategori')->orderBy('kategori')->pluck('kategori');

        return view('apoteker.obat', compact('obats', 'kategoriList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode'         => 'nullable|string|max:20|unique:obat,kode',
            'nama'         => 'required|string|max:100',
            'satuan'       => 'nullable|string|max:20',
            'kategori'     => 'nullable|string|max:50',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'harga'        => 'required|numeric|min:0',
            'keterangan'   => 'nullable|string',
        ]);

        $obat = Obat::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil ditambahkan.',
            'obat'    => $obat,
        ]);
    }

    public function update(Request $request, $id)
    {
        $obat = Obat::findOrFail($id);

        $validated = $request->validate([
            'kode'         => 'nullable|string|max:20|unique:obat,kode,' . $id,
            'nama'         => 'required|string|max:100',
            'satuan'       => 'nullable|string|max:20',
            'kategori'     => 'nullable|string|max:50',
            'stok'         => 'required|integer|min:0',
            'stok_minimum' => 'required|integer|min:0',
            'harga'        => 'required|numeric|min:0',
            'keterangan'   => 'nullable|string',
        ]);

        $obat->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data obat berhasil diperbarui.',
            'obat'    => $obat,
        ]);
    }

    public function destroy($id)
    {
        $obat = Obat::findOrFail($id);
        $obat->delete();

        return response()->json([
            'success' => true,
            'message' => 'Obat berhasil dihapus.',
        ]);
    }
}
