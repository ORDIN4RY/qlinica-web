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

    /** Tampilkan halaman daftar ICD-X — selalu dari database. */
    public function index(Request $request)
    {
        $search  = $request->input('search');
        $perPage = $request->input('per_page', 25);

        $query = Icdx::orderBy('kode');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%");
            });
        }

        $icdxs = $query->paginate($perPage)->withQueryString();
        $total  = Icdx::count();

        return view('icdx', compact('icdxs', 'search', 'total', 'perPage'));
    }

    /** Sync dari WHO API — dua mode:
     *  mode=all   → jalankan Artisan command (traversal hierarki penuh, lebih lama)
     *  mode=quick → search A-Z untuk coverage luas (cepat, via web request)
     */
    public function sync(Request $request)
    {
        $mode = $request->input('mode', 'quick');

        if ($mode === 'all') {
            // Jalankan Artisan command secara sinkron
            // (untuk produksi sebaiknya pakai Queue, tapi ini cukup untuk skala kecil)
            try {
                \Artisan::call('icdx:sync', ['--depth' => 5, '--delay' => 100]);
                $output = \Artisan::output();
                return redirect()->route('admin.icdx')
                    ->with('success', 'Sync penuh selesai! ' . Icdx::count() . ' total data ICD-X di database.');
            } catch (\Exception $e) {
                return redirect()->route('admin.icdx')
                    ->with('error', 'Sync gagal: ' . $e->getMessage());
            }
        }

        // Mode quick: search A-Z + a-z untuk coverage luas
        $letters = array_merge(
            range('A', 'Z'),
            ['fever', 'pain', 'disorder', 'disease', 'syndrome', 'infection', 'failure', 'injury', 'cancer', 'tumor']
        );

        $synced  = 0;
        $skipped = 0;
        $errors  = [];

        foreach ($letters as $keyword) {
            try {
                $data     = $this->icd->search((string) $keyword);
                $entities = $data['DestinationEntities'] ?? [];

                foreach ($entities as $entity) {
                    $kode = trim($entity['theCode'] ?? '');
                    $nama = trim(strip_tags($entity['Title'] ?? ''));

                    if (!$kode || !$nama) { $skipped++; continue; }

                    Icdx::updateOrCreate(['kode' => $kode], ['nama' => $nama]);
                    $synced++;
                }
            } catch (\Exception $e) {
                $errors[] = "'{$keyword}': " . $e->getMessage();
            }
        }

        $msg = "Sync cepat selesai: {$synced} data disimpan/diperbarui, {$skipped} dilewati. Total DB: " . Icdx::count();
        if (!empty($errors)) {
            $msg .= ' | Error: ' . implode('; ', array_slice($errors, 0, 3));
        }

        return redirect()->route('admin.icdx')->with('success', $msg);
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
