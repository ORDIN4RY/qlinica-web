<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Dokter',
            'Perawat',
            'Resepsionis',
            'Apoteker',
            'OB',
            'Satpam',
            'Teknisi'
        ];

        foreach ($data as $item) {
            DB::table('jabatan')->updateOrInsert(
                ['nama_jabatan' => $item],
                ['nama_jabatan' => $item, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
