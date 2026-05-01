<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AgamaSeeder::class,
            PendidikanSeeder::class,
            PekerjaanSeeder::class,
            AdminSeeder::class,
            PegawaiSeeder::class,
            PasienSeeder::class,
            IcdxSeeder::class,
        ]);
    }
}
