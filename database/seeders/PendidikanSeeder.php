<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pendidikan;

class PendidikanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Tidak Sekolah',
            'SD',
            'SMP',
            'SMA/SMK',
            'D1/D2/D3',
            'S1/D4',
            'S2',
            'S3'
        ];

        foreach ($data as $item) {
            Pendidikan::firstOrCreate(['pendidikan' => $item]);
        }
    }
}
