<?php

namespace Database\Seeders;

use App\Models\Shift;
use Illuminate\Database\Seeder;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        $shifts = [
            [
                'nama_shift' => 'Shift Pagi',
                'jam_masuk'  => '07:00:00',
                'jam_pulang' => '14:00:00',
            ],
            [
                'nama_shift' => 'Shift Sore',
                'jam_masuk'  => '14:00:00',
                'jam_pulang' => '21:00:00',
            ],
            [
                'nama_shift' => 'Shift Malam',
                'jam_masuk'  => '21:00:00',
                'jam_pulang' => '07:00:00',
            ],
        ];

        foreach ($shifts as $s) {
            Shift::updateOrCreate(['nama_shift' => $s['nama_shift']], $s);
        }
    }
}
