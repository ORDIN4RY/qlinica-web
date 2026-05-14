<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pegawai;

class PegawaiSeeder extends Seeder
{
    public function run(): void
    {
        // Dokter
        $userDokter = User::updateOrCreate(
            ['email' => 'dokter@sahaduta.com'],
            [
                'name'      => 'Dr. Ahmad Wijaya',
                'email'     => 'dokter@sahaduta.com',
                'password'  => Hash::make('password'),
                'role'      => 'pegawai',
                'is_active' => true,
                'phone'     => '081234567890',
            ]
        );

        Pegawai::updateOrCreate(
            ['user_id' => $userDokter->id],
            [
                'nik'         => '1234567890123456',
                'nama'        => 'Dr. Ahmad Wijaya',
                'spesialisasi' => 'Umum',
                'jabatan_id' => '1',
                'no_sip'      => 'SIP/2026/001',
                'jabatan_id'   => 2, // Dokter
                'alamat'      => 'Jl. Kesehatan No. 1, Jakarta',
                'no_hp'       => '081234567890',
            ]
        );

        // Apoteker
        $userApoteker = User::updateOrCreate(
            ['email' => 'apoteker@sahaduta.com'],
            [
                'name'      => 'Siti Nurhaliza',
                'email'     => 'apoteker@sahaduta.com',
                'password'  => Hash::make('password'),
                'role'      => 'pegawai',
                'is_active' => true,
                'phone'     => '081234567891',
            ]
        );

        Pegawai::updateOrCreate(
            ['user_id' => $userApoteker->id],
            [
                'nik'         => '1234567890123457',
                'nama'        => 'Siti Nurhaliza',
                'spesialisasi' => 'Apoteker',
                'jabatan_id'   => 5, // Apoteker
                'no_sip'      => 'SIP/2026/002',
                'alamat'      => 'Jl. Kesehatan No. 2, Jakarta',
                'no_hp'       => '081234567891',
            ]
        );

        // Perawat
        $userPerawat = User::updateOrCreate(
            ['email' => 'perawat@sahaduta.com'],
            [
                'name'      => 'Maya Sari',
                'email'     => 'perawat@sahaduta.com',
                'password'  => Hash::make('password'),
                'role'      => 'pegawai',
                'is_active' => true,
                'phone'     => '081234567892',
            ]
        );

        Pegawai::updateOrCreate(
            ['user_id' => $userPerawat->id],
            [
                'nik'         => '1234567890123458',
                'nama'        => 'Maya Sari',
                'spesialisasi' => 'Perawat',
                'jabatan_id'   => 3, // Perawat
                'no_sip'      => 'SIP/2026/003',
                'alamat'      => 'Jl. Kesehatan No. 3, Jakarta',
                'no_hp'       => '081234567892',
            ]
        );
    }
}
