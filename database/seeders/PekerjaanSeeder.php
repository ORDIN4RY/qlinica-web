<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pekerjaan;

class PekerjaanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Belum/Tidak Bekerja',
            'Pelajar/Mahasiswa',
            'Mengurus Rumah Tangga',
            'PNS',
            'TNI/Polri',
            'Karyawan Swasta',
            'Karyawan BUMN/BUMD',
            'Wiraswasta/Pengusaha',
            'Petani/Pekebun',
            'Nelayan',
            'Buruh',
            'Lainnya'
        ];

        foreach ($data as $item) {
            Pekerjaan::firstOrCreate(['pekerjaan' => $item]);
        }
    }
}
