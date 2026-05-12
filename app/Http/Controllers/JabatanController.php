<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Menu;
use App\Models\HakAkses;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    /** Halaman kelola jabatan dan hak akses. */
    public function index()
    {
        $jabatans = Jabatan::withCount('pegawais')->orderBy('nama_jabatan')->get();
        $menus    = Menu::orderBy('nama_menu')->get();

        $hakAkses = HakAkses::with('menu')
            ->get()
            ->groupBy('jabatan_id')
            ->map(fn($items) => $items->keyBy('menu_id'));

        return view('admin.jabatan', compact('jabatans', 'menus', 'hakAkses'));
    }

    /** Tambah jabatan baru. */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100|unique:jabatan,nama_jabatan',
        ], [
            'nama_jabatan.required' => 'Nama jabatan wajib diisi.',
            'nama_jabatan.unique'   => 'Nama jabatan sudah ada.',
            'nama_jabatan.max'      => 'Nama jabatan maksimal 100 karakter.',
        ]);

        Jabatan::create(['nama_jabatan' => $request->nama_jabatan]);

        return redirect()->route('admin.jabatan')
            ->with('success', 'Jabatan "' . $request->nama_jabatan . '" berhasil ditambahkan.');
    }

    /** Hapus jabatan — hanya jika tidak ada pegawai aktif. */
    public function destroy($id)
    {
        $jabatan = Jabatan::withCount('pegawais')->findOrFail($id);

        if ($jabatan->pegawais_count > 0) {
            return redirect()->route('admin.jabatan')
                ->with('error', 'Jabatan "' . $jabatan->nama_jabatan . '" tidak dapat dihapus karena masih memiliki ' . $jabatan->pegawais_count . ' pegawai. Ubah jabatan pegawai tersebut terlebih dahulu.');
        }

        DB::transaction(function () use ($jabatan) {
            HakAkses::where('jabatan_id', $jabatan->id)->delete();
            $jabatan->delete();
        });

        return redirect()->route('admin.jabatan')
            ->with('success', 'Jabatan "' . $jabatan->nama_jabatan . '" berhasil dihapus.');
    }

    /** Update hak akses untuk satu jabatan. */
    public function updateAkses(Request $request, $id)
    {
        $jabatan = Jabatan::findOrFail($id);

        $request->validate([
            'akses'   => 'nullable|array',
            'akses.*' => 'nullable|array',
        ]);

        $aksesData = $request->input('akses', []);

        DB::transaction(function () use ($jabatan, $aksesData) {
            HakAkses::where('jabatan_id', $jabatan->id)->delete();

            foreach ($aksesData as $menuId => $flags) {
                $menuId = (int) $menuId;
                if (!$menuId) continue;

                if (!empty($flags['lihat'])) {
                    HakAkses::create([
                        'jabatan_id'  => $jabatan->id,
                        'menu_id'     => $menuId,
                        'bisa_lihat'  => true,
                        'bisa_tambah' => !empty($flags['tambah']),
                        'bisa_edit'   => !empty($flags['edit']),
                        'bisa_hapus'  => !empty($flags['hapus']),
                    ]);
                }
            }
        });

        return redirect()->route('admin.jabatan')
            ->with('success', 'Hak akses untuk jabatan "' . $jabatan->nama_jabatan . '" berhasil diperbarui.');
    }
}
