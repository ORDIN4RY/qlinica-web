<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sahaduta.com'],
            [
                'name'     => 'Admin Sahaduta',
                'email'    => 'admin@sahaduta.com',
                'password' => Hash::make('password'),
            ]
        );
    }
}
