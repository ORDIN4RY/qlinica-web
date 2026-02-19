<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

// ============================================================
// AGAMA SEEDER
// ============================================================
class AgamaSeeder extends Seeder
{
    public function run(): void
    {
        $agama = ['Islam', 'Kristen Protestan', 'Kristen Katolik', 'Hindu', 'Buddha', 'Konghucu'];
        foreach ($agama as $item) {
            DB::table('agama')->insertOrIgnore([
                'agama'      => $item,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

// ============================================================
// PENDIDIKAN SEEDER
// ============================================================
class PendidikanSeeder extends Seeder
{
    public function run(): void
    {
        $data = ['Tidak Sekolah', 'SD', 'SMP', 'SMA/SMK', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3'];
        foreach ($data as $item) {
            DB::table('pendidikan')->insertOrIgnore([
                'pendidikan' => $item,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

// ============================================================
// PEKERJAAN SEEDER
// ============================================================
class PekerjaanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'PNS', 'TNI', 'POLRI', 'Pegawai Swasta', 'Wiraswasta',
            'Petani', 'Nelayan', 'Buruh', 'Ibu Rumah Tangga', 'Pelajar/Mahasiswa', 'Tidak Bekerja',
        ];
        foreach ($data as $item) {
            DB::table('pekerjaan')->insertOrIgnore([
                'pekerjaan'  => $item,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

// ============================================================
// JENIS KELAMIN SEEDER
// ============================================================
class JenisKelaminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('jenis_kelamin')->insertOrIgnore([
            ['jenis_kelamin' => 'Laki-laki', 'created_at' => now(), 'updated_at' => now()],
            ['jenis_kelamin' => 'Perempuan',  'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

// ============================================================
// USER SEEDER — Akun default untuk setiap role
// ============================================================
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'       => 'Administrator',
                'email'      => 'admin@rsapp.com',
                'password'   => Hash::make('password'),
                'role'       => 'admin',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'dr. Budi Santoso',
                'email'      => 'dokter@rsapp.com',
                'password'   => Hash::make('password'),
                'role'       => 'dokter',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Sari Perawat',
                'email'      => 'perawat@rsapp.com',
                'password'   => Hash::make('password'),
                'role'       => 'perawat',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Ahmad Apoteker',
                'email'      => 'apoteker@rsapp.com',
                'password'   => Hash::make('password'),
                'role'       => 'apoteker',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            $userId = DB::table('users')->insertGetId($user);

            // Buat data pegawai untuk semua role kecuali pasien
            DB::table('pegawai')->insert([
                'user_id'       => $userId,
                'nama'          => $user['name'],
                'spesialisasi'  => $user['role'] === 'dokter' ? 'Umum' : null,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }
    }
}

