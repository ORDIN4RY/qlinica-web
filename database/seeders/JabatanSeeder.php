<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_jabatan' => 'Admin',        'jenis' => 'non-medis'],
            ['nama_jabatan' => 'Dokter',        'jenis' => 'medis'],
            ['nama_jabatan' => 'Perawat',       'jenis' => 'medis'],
            ['nama_jabatan' => 'Resepsionis',   'jenis' => 'non-medis'],
            ['nama_jabatan' => 'Apoteker',      'jenis' => 'medis'],
            ['nama_jabatan' => 'OB',            'jenis' => 'non-medis'],
            ['nama_jabatan' => 'Satpam',        'jenis' => 'non-medis'],
            ['nama_jabatan' => 'Teknisi',       'jenis' => 'non-medis'],
        ];

        foreach ($data as $item) {
            DB::table('jabatan')->updateOrInsert(
                ['nama_jabatan' => $item['nama_jabatan']],
                array_merge($item, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
