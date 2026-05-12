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
                'Dashboard'   => [1,1,1,1],
                'Pasien'      => [1,1,1,1],
                'Pegawai'     => [1,1,1,1],
                'Antrian'     => [1,1,1,1],
                'Resep'       => [1,1,1,1],
                'Obat'        => [1,1,1,1],
                'ICDX'        => [1,1,1,1],
                'Laporan'     => [1,1,1,1],
                'Komentar'    => [1,1,1,1],
                'Rekam Medis' => [1,1,1,1],
                'Jabatan'     => [1,1,1,1],
            ],
            'Dokter' => [
                'Dashboard'   => [1,0,0,0],
                'Pasien'      => [1,0,1,0],
                'Antrian'     => [1,1,1,0],
                'Resep'       => [1,0,1,0],
                'Rekam Medis' => [1,1,1,0],
                'Laporan'     => [1,0,0,0],
            ],
            'Perawat' => [
                'Dashboard'   => [1,0,0,0],
                'Pasien'      => [1,0,0,0],
                'Antrian'     => [1,1,1,0],
                'Laporan'     => [1,0,0,0],
            ],
            'Apoteker' => [
                'Dashboard'   => [1,0,0,0],
                'Resep'       => [1,1,1,0],
                'Obat'        => [1,1,1,1],
                'Laporan'     => [1,0,0,0],
            ],
            'Resepsionis' => [
                'Dashboard'   => [1,0,0,0],
                'Antrian'     => [1,1,1,0],
                'Pasien'      => [1,1,1,0],
            ],
            'OB' => [
                'Dashboard'   => [1,0,0,0],
            ],
            'Satpam' => [
                'Dashboard'   => [1,0,0,0],
            ],
            'Teknisi' => [
                'Dashboard'   => [1,0,0,0],
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
