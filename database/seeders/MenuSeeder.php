<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            'Dashboard Admin',
            'Dashboard Dokter',
            'Dashboard Apoteker',
            'Data Pasien',
            'Antrian & Pemesanan',
            'Rekam Medis & Diagnosa (Dokter)',
            'Resep Obat (Apoteker)',
            'Data Obat (Apoteker)',
            'Laporan Apotek (Apoteker)',
            'Data Pegawai (Admin)',
            'Presensi Pegawai (Admin)',
            'Data ICD-X (Admin)',
            'Laporan Penanganan (Admin)',
            'Komentar & Feedback (Admin)',
            'Jabatan & Hak Akses (Admin)',
        ];

        foreach ($menus as $nama) {
            Menu::updateOrCreate(
                ['nama_menu' => $nama],
                ['nama_menu' => $nama]
            );
        }
    }
}
