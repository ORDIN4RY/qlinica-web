<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Pegawai;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@sahaduta.com'],
            [
                'name'      => 'Admin Sahaduta',
                'email'     => 'admin@sahaduta.com',
                'password'  => Hash::make('password'),
                'role'      => 'pegawai',
                'is_active' => true,
                'phone'     => null,
            ]
        );

        Pegawai::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nik'         => '0000000000000000',
                'nama'        => 'Admin Sahaduta',
                'jabatan_id'  => 1, // Admin
                'alamat'      => 'Klinik Sahaduta',
                'no_hp'       => '081000000000',
            ]
        );
    }
}
