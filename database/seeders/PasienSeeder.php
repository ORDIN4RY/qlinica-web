<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PasienSeeder extends Seeder
{
    public function run(): void
    {
        // Buat atau ambil user untuk pasien demo
        $user = DB::table('users')->where('email', 'pasien@sahaduta.com')->first();

        if (!$user) {
            $userId = DB::table('users')->insertGetId([
                'name'       => 'Pasien Demo',
                'email'      => 'pasien@sahaduta.com',
                'password'   => Hash::make('password'),
                'role'       => 'pasien',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $userId = $user->id;
        }

        // Buat data pasien hanya jika belum ada
        $exists = DB::table('pasien')->where('no_rm', 'RM-20260101-0001')->exists();

        if (!$exists) {
            DB::table('pasien')->insert([
                'user_id'       => $userId,
                'no_rm'         => 'RM-20260101-0001',
                'nik'           => '3201010101010001',
                'nama'          => 'Pasien Demo',
                'tgl_lahir'     => '1990-01-01',
                'jenis_kelamin' => 'L',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}
