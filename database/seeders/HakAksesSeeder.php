<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;
use App\Models\Menu;
use App\Models\HakAkses;

class HakAksesSeeder extends Seeder
{
    public function run(): void
    {
        $menuMap = Menu::pluck('id', 'nama_menu');
        $jabatanMap = Jabatan::pluck('id', 'nama_jabatan');

        // Definisi hak akses per jabatan
        // Format: nama_jabatan => [ nama_menu => [lihat, tambah, edit, hapus], ... ]
        $akses = [
            'Admin' => [
                'Dashboard Admin'             => [1,1,1,1],
                'Data Pasien'                 => [1,1,1,1],
                'Data Pegawai (Admin)'        => [1,1,1,1],
                'Presensi Pegawai (Admin)'    => [1,1,1,1],
                'Antrian & Pemesanan'         => [1,1,1,1],
                'Data ICD-X (Admin)'          => [1,1,1,1],
                'Laporan Penanganan (Admin)'  => [1,1,1,1],
                'Komentar & Feedback (Admin)' => [1,1,1,1],
                'Jabatan & Hak Akses (Admin)' => [1,1,1,1],
            ],
            'Dokter' => [
                'Dashboard Dokter'            => [1,0,0,0],
                'Data Pasien'                 => [1,0,1,0],
                'Antrian & Pemesanan'         => [1,1,1,0],
                'Rekam Medis & Diagnosa (Dokter)' => [1,1,1,0],
            ],
            'Apoteker' => [
                'Dashboard Apoteker'          => [1,0,0,0],
                'Resep Obat (Apoteker)'       => [1,1,1,0],
                'Data Obat (Apoteker)'        => [1,1,1,1],
                'Laporan Apotek (Apoteker)'   => [1,0,0,0],
            ],
            'Perawat' => [
                'Dashboard Admin'             => [1,0,0,0],
                'Antrian & Pemesanan'         => [1,1,1,0],
            ],
            'Resepsionis' => [
                'Dashboard Admin'             => [1,0,0,0],
                'Antrian & Pemesanan'         => [1,1,1,0],
                'Data Pasien'                 => [1,1,1,0],
            ],
        ];

        foreach ($akses as $namaJabatan => $menuList) {
            if (!isset($jabatanMap[$namaJabatan])) {
                continue;
            }
            $jabatanId = $jabatanMap[$namaJabatan];

            foreach ($menuList as $namaMenu => $flags) {
                if (!isset($menuMap[$namaMenu])) {
                    continue;
                }
                $menuId = $menuMap[$namaMenu];

                HakAkses::updateOrCreate(
                    ['jabatan_id' => $jabatanId, 'menu_id' => $menuId],
                    [
                        'jabatan_id'  => $jabatanId,
                        'menu_id'     => $menuId,
                        'bisa_lihat'  => $flags[0],
                        'bisa_tambah' => $flags[1],
                        'bisa_edit'   => $flags[2],
                        'bisa_hapus'  => $flags[3],
                    ]
                );
            }
        }
    }
}
