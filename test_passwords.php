<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test creating user with number password
$u1 = \App\Models\User::create([
    'name' => 'Test',
    'email' => 'test1@sahaduta.com',
    'password' => '12345678', // with cast should hash
    'role' => 'pasien'
]);

$u2 = \App\Models\User::create([
    'name' => 'Test2',
    'email' => 'test2@sahaduta.com',
    'password' => \Illuminate\Support\Facades\Hash::make('12345678'), 
    'role' => 'pasien'
]);

echo "u1: " . $u1->getRawOriginal('password') . "\n";
echo "u2: " . $u2->getRawOriginal('password') . "\n";

\App\Models\User::whereIn('email', ['test1@sahaduta.com', 'test2@sahaduta.com'])->delete();
