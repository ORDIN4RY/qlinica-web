<?php

namespace App\Http\Controllers;

use App\Models\Icdx;
use App\Services\IcdService;
use Illuminate\Http\Request;

class IcdxController extends Controller
{
    public function __construct(protected IcdService $icd) {}

    public function searchApi(Request $request)
    {
        $request->validate(['q' => 'required|string|min:2']);
        try {
            return response()->json($this->icd->search($request->q));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function detailApi(string $code)
    {
        try {
            return response()->json($this->icd->getByCode($code));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /** Tampilkan halaman daftar ICD-X. */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $perPage = $request->input('per_page', 25);
        
        $apiResults = null;
        $error = null;

        if ($search) {
            // Jika ada pencarian, ambil dari API WHO
            try {
                $data = $this->icd->search($search);
                if (isset($data['DestinationEntities'])) {
                    $apiResults = collect($data['DestinationEntities'])
                        ->filter(fn($item) => !empty($item['theCode']))
                        ->map(function($item) {
                            return (object) [
                                'kode' => trim($item['theCode'] ?? ''),
                                'nama' => trim(strip_tags($item['Title'] ?? '')),
                                'is_api' => true
                            ];
                        })->values();
                } else {
                    $apiResults = collect([]);
                }
            } catch (\Exception $e) {
                $error = "Gagal menghubungi API WHO: " . $e->getMessage();
                $apiResults = collect([]);
            }
            $icdxs = $apiResults;
            $total = count($apiResults);
        } else {
            // Jika tidak ada pencarian, ambil dari database lokal
            $icdxs = Icdx::orderBy('kode')
                ->paginate($perPage)
                ->withQueryString();
            $total = Icdx::count();
        }

        return view('icdx', compact('icdxs', 'search', 'total', 'perPage', 'error'));
    }

    /** Simpan data ICD-X baru. */
    public function store(Request $request)
    {
        $request->merge([
            'kode' => trim($request->input('kode', '')),
            'nama' => trim($request->input('nama', '')),
        ]);

        $request->validate([
            'kode' => 'required|string|max:10|unique:icdx,kode',
            'nama' => 'required|string|max:255',
        ], [
            'kode.required' => 'Kode ICD-X wajib diisi.',
            'kode.unique'   => 'Kode ICD-X sudah terdaftar.',
            'nama.required' => 'Nama diagnosa wajib diisi.',
        ]);

        Icdx::create($request->only('kode', 'nama'));

        return redirect()->route('admin.icdx')->with('success', 'Data ICD-X berhasil ditambahkan.');
    }

    /** Update data ICD-X. */
    public function update(Request $request, $id)
    {
        $icdx = Icdx::findOrFail($id);

        $request->merge([
            'kode' => trim($request->input('kode', '')),
            'nama' => trim($request->input('nama', '')),
        ]);

        $request->validate([
            'kode' => "required|string|max:10|unique:icdx,kode,{$id}",
            'nama' => 'required|string|max:255',
        ], [
            'kode.required' => 'Kode ICD-X wajib diisi.',
            'kode.unique'   => 'Kode ICD-X sudah digunakan.',
            'nama.required' => 'Nama diagnosa wajib diisi.',
        ]);

        $icdx->update($request->only('kode', 'nama'));

        return redirect()->route('admin.icdx')->with('success', 'Data ICD-X berhasil diperbarui.');
    }

    /** Hapus data ICD-X. */
    public function destroy($id)
    {
        $icdx = Icdx::findOrFail($id);
        $icdx->delete();

        return redirect()->route('admin.icdx')->with('success', 'Data ICD-X berhasil dihapus.');
    }
}
