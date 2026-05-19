<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HakAkses;
use App\Models\Menu;

class HakAksesSeeder extends Seeder
{
    public function run(): void
    {
        HakAkses::truncate();

        // Mendapatkan ID menu berdasarkan namanya untuk kemudahan
        $menus = Menu::pluck('id', 'nama_menu');

        // 1 = Dokter
        // 2 = Perawat
        // 3 = Resepsionis
        // 4 = Apoteker

        $akses = [
            // Hak Akses Dokter
            [
                'jabatan_id' => 2,
                'menu_id' => $menus['Dashboard'],
                'sub_akses' => ['view' => true, 'dokter_dashboard' => true],
            ],
            [
                'jabatan_id' => 2,
                'menu_id' => $menus['Pasien'],
                'sub_akses' => ['view' => true],
            ],
            [
                'jabatan_id' => 2,
                'menu_id' => $menus['Antrian Pemeriksaan'],
                'sub_akses' => ['view' => true],
            ],
            [
                'jabatan_id' => 2,
                'menu_id' => $menus['Rekam Medis'],
                'sub_akses' => ['view' => true, 'tambah' => true, 'edit' => true],
            ],
            [
                'jabatan_id' => 2,
                'menu_id' => $menus['ICDX'],
                'sub_akses' => ['view' => true],
            ],
            [
                'jabatan_id' => 2,
                'menu_id' => $menus['Rawat Inap'],
                'sub_akses' => ['view' => true],
            ],
            [
                'jabatan_id' => 2,
                'menu_id' => $menus['Kamar'],
                'sub_akses' => ['view' => true],
            ],
            [
                'jabatan_id' => 2,
                'menu_id' => $menus['Laporan'],
                'sub_akses' => ['view' => true, 'penanganan' => true],
            ],

            // Hak Akses Perawat
            [
                'jabatan_id' => 3,
                'menu_id' => $menus['Dashboard'],
                'sub_akses' => ['view' => true, 'dokter_dashboard' => true],
            ],
            [
                'jabatan_id' => 3,
                'menu_id' => $menus['Pasien'],
                'sub_akses' => ['view' => true],
            ],
            [
                'jabatan_id' => 3,
                'menu_id' => $menus['Rekam Medis'],
                'sub_akses' => ['view' => true],
            ],

            // Hak Akses Resepsionis
            [
                'jabatan_id' => 4,
                'menu_id' => $menus['Dashboard'],
                'sub_akses' => ['view' => true, 'admin_dashboard' => true],
            ],
            [
                'jabatan_id' => 4,
                'menu_id' => $menus['Pasien'],
                'sub_akses' => ['view' => true, 'tambah' => true, 'edit' => true, 'hapus' => true],
            ],
            [
                'jabatan_id' => 4,
                'menu_id' => $menus['Antrian Pemesanan'],
                'sub_akses' => ['view' => true, 'tambah' => true, 'update' => true],
            ],
            [
                'jabatan_id' => 4,
                'menu_id' => $menus['Billing'],
                'sub_akses' => ['view' => true, 'bayar' => true, 'bpjs' => true],
            ],
            [
                'jabatan_id' => 4,
                'menu_id' => $menus['Kamar'],
                'sub_akses' => ['view' => true, 'tambah' => true, 'edit' => true, 'hapus' => true],
            ],
            [
                'jabatan_id' => 4,
                'menu_id' => $menus['Rawat Inap'],
                'sub_akses' => ['view' => true, 'tambah' => true, 'edit' => true],
            ],

            // Hak Akses Apoteker
            [
                'jabatan_id' => 5,
                'menu_id' => $menus['Dashboard'],
                'sub_akses' => ['view' => true, 'apoteker_dashboard' => true],
            ],
            [
                'jabatan_id' => 5,
                'menu_id' => $menus['Resep'],
                'sub_akses' => ['view' => true, 'edit' => true],
            ],
            [
                'jabatan_id' => 5,
                'menu_id' => $menus['Obat'],
                'sub_akses' => ['view' => true, 'tambah' => true, 'edit' => true, 'hapus' => true],
            ],
            [
                'jabatan_id' => 5,
                'menu_id' => $menus['Pasien'],
                'sub_akses' => ['view' => true],
            ],
            [
                'jabatan_id' => 5,
                'menu_id' => $menus['Laporan'],
                'sub_akses' => ['view' => true, 'apotek' => true],
            ],
        ];

        foreach ($akses as $data) {
            HakAkses::create($data);
        }
    }
}
