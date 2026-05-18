<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Menu;
use App\Models\HakAkses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    /**
     * Definisi sub-akses untuk setiap menu.
     * Format: 'key' => ['label' => '...', 'icon' => 'fa-...', 'desc' => '...']
     * Key 'view' wajib ada — ini adalah toggle utama menu.
     *
     * Key khusus yang dikenali oleh middleware:
     *   view, tambah, edit, hapus
     * Key bebas untuk sub-akses spesifik menu (misal dashboard type).
     */
    public static function menuSubAkses(): array
    {
        return [
            'Dashboard' => [
                'view' => [
                    'label' => 'Akses Dashboard',
                    'icon'  => 'fa-chart-pie',
                    'desc'  => 'Izinkan akses ke halaman dashboard',
                ],
                'admin_dashboard' => [
                    'label' => 'Tampilan Admin',
                    'icon'  => 'fa-shield-alt',
                    'desc'  => 'Dashboard ringkasan admin (statistik, antrian, dsb)',
                    'preview' => 'dashboard_admin',
                ],
                'dokter_dashboard' => [
                    'label' => 'Tampilan Dokter',
                    'icon'  => 'fa-stethoscope',
                    'desc'  => 'Dashboard dokter (antrian pasien, jadwal)',
                    'preview' => 'dashboard_dokter',
                ],
                'apoteker_dashboard' => [
                    'label' => 'Tampilan Apoteker',
                    'icon'  => 'fa-pills',
                    'desc'  => 'Dashboard apoteker (resep, obat)',
                    'preview' => 'dashboard_apoteker',
                ],
            ],
            'Antrian Pemesanan' => [
                'view'   => ['label' => 'Lihat Antrian',   'icon' => 'fa-eye',        'desc' => 'Melihat daftar antrian pasien'],
                'tambah' => ['label' => 'Tambah Antrian',  'icon' => 'fa-plus',       'desc' => 'Mendaftarkan antrian baru'],
                'update'   => ['label' => 'Update Status',   'icon' => 'fa-pen',        'desc' => 'Mengubah status & memanggil antrian'],
            ],
            'Antrian Pemeriksaan' => [
                'view'   => ['label' => 'Lihat Antrian',          'icon' => 'fa-eye',    'desc' => 'Melihat antrian pemeriksaan yang ditugaskan kepada dokter bersangkutan'],
                'all'    => ['label' => 'Semua Antrian',         'icon' => 'fa-list',   'desc' => 'Melihat seluruh antrian pemeriksaan dari semua dokter'],
            ],
            'Pasien' => [
                'view'   => ['label' => 'Lihat Data',      'icon' => 'fa-eye',        'desc' => 'Melihat daftar & detail data pasien'],
                'tambah' => ['label' => 'Tambah Pasien',   'icon' => 'fa-user-plus',  'desc' => 'Mendaftarkan pasien baru'],
                'edit'   => ['label' => 'Edit Data',       'icon' => 'fa-pen',        'desc' => 'Mengubah data pasien'],
                'hapus'  => ['label' => 'Hapus Data',      'icon' => 'fa-trash',      'desc' => 'Menghapus data pasien'],
                // 'akun'   => ['label' => 'Kelola Akun',     'icon' => 'fa-key',        'desc' => 'Membuat akun login pasien'],
            ],
            'Pegawai' => [
                'view'   => ['label' => 'Lihat Data',      'icon' => 'fa-eye',        'desc' => 'Melihat daftar & detail pegawai'],
                'tambah' => ['label' => 'Tambah Pegawai',  'icon' => 'fa-user-plus',  'desc' => 'Mendaftarkan pegawai baru'],
                'edit'   => ['label' => 'Edit Data',       'icon' => 'fa-pen',        'desc' => 'Mengubah data pegawai'],
                'hapus'  => ['label' => 'Hapus Data',      'icon' => 'fa-trash',      'desc' => 'Menghapus data pegawai'],
            ],
            'Presensi' => [
                'view'   => ['label' => 'Lihat Data',      'icon' => 'fa-eye',        'desc' => 'Melihat data presensi pegawai'],
            ],
            'Resep' => [
                'view'   => ['label' => 'Lihat Resep',     'icon' => 'fa-eye',        'desc' => 'Melihat daftar resep masuk'],
                'edit'   => ['label' => 'Proses Resep',    'icon' => 'fa-check',      'desc' => 'Mengubah status & memproses resep'],
            ],
            'Obat' => [
                'view'   => ['label' => 'Lihat Stok Obat', 'icon' => 'fa-eye',        'desc' => 'Melihat data stok obat'],
                'tambah' => ['label' => 'Tambah Obat',     'icon' => 'fa-plus',       'desc' => 'Menambah data obat baru'],
                'edit'   => ['label' => 'Edit Obat',       'icon' => 'fa-pen',        'desc' => 'Mengubah data obat'],
                'hapus'  => ['label' => 'Hapus Obat',      'icon' => 'fa-trash',      'desc' => 'Menghapus data obat'],
            ],
            'ICDX' => [
                'view'   => ['label' => 'Lihat ICD-X',     'icon' => 'fa-eye',        'desc' => 'Melihat daftar kode ICD-X'],
                'tambah' => ['label' => 'Tambah Kode',     'icon' => 'fa-plus',       'desc' => 'Menambah kode ICD-X baru'],
                'edit'   => ['label' => 'Edit Kode',       'icon' => 'fa-pen',        'desc' => 'Mengubah kode ICD-X'],
                'hapus'  => ['label' => 'Hapus Kode',      'icon' => 'fa-trash',      'desc' => 'Menghapus kode ICD-X'],
            ],
            'Laporan' => [
                'view'      => ['label' => 'Lihat Laporan',     'icon' => 'fa-eye',        'desc' => 'Akses halaman laporan'],
                'penanganan'=> ['label' => 'Lap. Penanganan',   'icon' => 'fa-chart-line',  'desc' => 'Laporan data penanganan pasien'],
                'export'    => ['label' => 'Export Data',       'icon' => 'fa-file-export', 'desc' => 'Export laporan ke PDF/Excel'],
            ],
            'Komentar' => [
                'view'   => ['label' => 'Lihat Komentar',  'icon' => 'fa-eye',        'desc' => 'Melihat komentar & ulasan pasien'],
                'hapus'  => ['label' => 'Hapus Komentar',  'icon' => 'fa-trash',      'desc' => 'Menghapus komentar'],
            ],
            'Jabatan' => [
                'view'   => ['label' => 'Lihat Jabatan',   'icon' => 'fa-eye',        'desc' => 'Melihat daftar jabatan & hak akses'],
                'tambah' => ['label' => 'Tambah Jabatan',  'icon' => 'fa-plus',       'desc' => 'Membuat jabatan baru'],
                'edit'   => ['label' => 'Edit Hak Akses',  'icon' => 'fa-pen',        'desc' => 'Mengubah hak akses jabatan'],
                'hapus'  => ['label' => 'Hapus Jabatan',   'icon' => 'fa-trash',      'desc' => 'Menghapus jabatan'],
            ],
            'Rekam Medis' => [
                'view'   => ['label' => 'Lihat Rekam Medis','icon' => 'fa-eye',       'desc' => 'Melihat riwayat rekam medis'],
                'tambah' => ['label' => 'Input Diagnosa',   'icon' => 'fa-plus',      'desc' => 'Menginput diagnosa & resep'],
                'edit'   => ['label' => 'Edit Diagnosa',    'icon' => 'fa-pen',       'desc' => 'Mengubah data diagnosa'],
            ],
            'Presensi' => [
                'view'    => ['label' => 'Lihat Presensi',  'icon' => 'fa-eye',       'desc' => 'Melihat data presensi pegawai'],
                'edit'    => ['label' => 'Setujui/Tolak',   'icon' => 'fa-check',     'desc' => 'Menyetujui atau menolak presensi'],
                'hapus'   => ['label' => 'Hapus Presensi',  'icon' => 'fa-trash',     'desc' => 'Menghapus data presensi'],
                'export'  => ['label' => 'Export Presensi', 'icon' => 'fa-file-export','desc' => 'Export data presensi ke Excel'],
            ],
        ];
    }

    /** Halaman kelola jabatan dan hak akses. */
    public function index()
    {
        $jabatans = Jabatan::withCount('pegawais')->orderBy('nama_jabatan')->get();
        $menus    = Menu::orderBy('nama_menu')->get();

        $hakAkses = HakAkses::with('menu')
            ->get()
            ->groupBy('jabatan_id')
            ->map(fn($items) => $items->keyBy('menu_id'));

        $menuSubAkses = self::menuSubAkses();

        return view('admin.jabatan', compact('jabatans', 'menus', 'hakAkses', 'menuSubAkses'));
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
                ->with('error', 'Jabatan "' . $jabatan->nama_jabatan . '" tidak dapat dihapus karena masih memiliki ' . $jabatan->pegawais_count . ' pegawai.');
        }

        DB::transaction(function () use ($jabatan) {
            HakAkses::where('jabatan_id', $jabatan->id)->delete();
            $jabatan->delete();
        });

        return redirect()->route('admin.jabatan')
            ->with('success', 'Jabatan "' . $jabatan->nama_jabatan . '" berhasil dihapus.');
    }

    /** Update hak akses (sub_akses JSON) untuk satu jabatan. */
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

            foreach ($aksesData as $menuId => $subKeys) {
                $menuId = (int) $menuId;
                if (!$menuId) continue;

                // Bangun array sub_akses dari checkbox yang dicentang
                $sub = [];
                foreach ($subKeys as $key => $val) {
                    if ($val == '1') {
                        $sub[$key] = true;
                    }
                }

                // Hanya simpan jika minimal 'view' aktif
                if (!empty($sub['view'])) {
                    HakAkses::create([
                        'jabatan_id'  => $jabatan->id,
                        'menu_id'     => $menuId,
                        'sub_akses'   => $sub,
                    ]);
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Hak akses jabatan "' . $jabatan->nama_jabatan . '" berhasil disimpan.']);
    }
}
