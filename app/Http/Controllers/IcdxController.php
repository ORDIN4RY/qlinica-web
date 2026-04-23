<?php

namespace App\Http\Controllers;

use App\Models\Icdx;
use Illuminate\Http\Request;

class IcdxController extends Controller
{
    /** Tampilkan halaman daftar ICD-X. */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 25);

        $icdxs = Icdx::when($search, function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('nama_en', 'like', "%{$search}%");
            })
            ->orderBy('kode')
            ->paginate($perPage)
            ->withQueryString();

        $total = Icdx::count();

        return view('icdx', compact('icdxs', 'search', 'total', 'perPage'));
    }

    /** Simpan data ICD-X baru. */
    public function store(Request $request)
    {
        $request->validate([
            'kode'    => 'required|string|max:10|unique:icdx,kode',
            'nama'    => 'required|string|max:255',
            'nama_en' => 'nullable|string|max:255',
        ], [
            'kode.required' => 'Kode ICD-X wajib diisi.',
            'kode.unique'   => 'Kode ICD-X sudah terdaftar.',
            'nama.required' => 'Nama diagnosa wajib diisi.',
        ]);

        Icdx::create($request->only('kode', 'nama', 'nama_en'));

        return redirect()->route('admin.icdx')->with('success', 'Data ICD-X berhasil ditambahkan.');
    }

    /** Update data ICD-X. */
    public function update(Request $request, $id)
    {
        $icdx = Icdx::findOrFail($id);

        $request->validate([
            'kode'    => "required|string|max:10|unique:icdx,kode,{$id}",
            'nama'    => 'required|string|max:255',
            'nama_en' => 'nullable|string|max:255',
        ], [
            'kode.required' => 'Kode ICD-X wajib diisi.',
            'kode.unique'   => 'Kode ICD-X sudah digunakan.',
            'nama.required' => 'Nama diagnosa wajib diisi.',
        ]);

        $icdx->update($request->only('kode', 'nama', 'nama_en'));

        return redirect()->back()->with('success', 'Data ICD-X berhasil diperbarui.');
    }

    /** Hapus data ICD-X. */
    public function destroy($id)
    {
        $icdx = Icdx::findOrFail($id);
        $icdx->delete();

        return redirect()->back()->with('success', 'Data ICD-X berhasil dihapus.');
    }
}
