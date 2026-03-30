<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Patient;
use Carbon\Carbon;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $diseases = [
            'ISPA', 'Hipertensi', 'Diabetes Mellitus', 'Dermatitis',
            'Diare', 'Gastritis', 'ISPA', 'Hipertensi', 'ISPA',
            'Demam Berdarah', 'Asma', 'Diabetes Mellitus', 'Hipertensi',
            'ISPA', 'Gastritis', 'Dermatitis', 'ISPA', 'Hipertensi',
        ];

        $names = [
            'Budi Santoso', 'Siti Rahayu', 'Ahmad Fauzi', 'Dewi Lestari',
            'Eko Prasetyo', 'Fitri Handayani', 'Galih Permana', 'Hani Susanti',
            'Irfan Maulana', 'Joko Widodo', 'Kartini Putri', 'Lukman Hakim',
            'Maya Sari', 'Nandi Kurniawan', 'Olga Tamara', 'Pandu Wibowo',
            'Qonita Amalia', 'Rizki Pratama',
        ];

        $year = Carbon::now()->year;

        foreach ($names as $i => $name) {
            $month = ($i % 12) + 1;
            $day   = rand(1, 28);
            Patient::create([
                'name'       => $name,
                'nik'        => '320' . str_pad($i + 1, 13, '0', STR_PAD_LEFT),
                'age'        => rand(18, 70),
                'gender'     => ($i % 2 === 0) ? 'L' : 'P',
                'address'    => 'Jl. Contoh No. ' . ($i + 1) . ', Jakarta',
                'phone'      => '08' . rand(100000000, 999999999),
                'disease'    => $diseases[$i],
                'visit_date' => Carbon::create($year, $month, $day)->toDateString(),
                'notes'      => null,
            ]);
        }
    }
}
