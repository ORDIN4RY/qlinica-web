<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            'Dashboard',
            'Pasien',
            'Pegawai',
            'Antrian Pemesanan',
            'Resep',
            'Obat',
            'ICDX',
            'Laporan',
            'Komentar',
            'Rekam Medis',
            'Jabatan',
            'Presensi',
            'Antrian Pemeriksaan',
        ];

        foreach ($menus as $nama) {
            Menu::updateOrCreate(
                ['nama_menu' => $nama],
                ['nama_menu' => $nama]
            );
        }
    }
}
