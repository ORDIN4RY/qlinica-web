<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "=== CEK USER ===\n";
$users = DB::table('users')->select('id','email','role')->get();
foreach ($users as $u) {
    echo "ID:{$u->id} Email:{$u->email} Role:{$u->role}\n";
}

echo "\n=== CEK PASIEN ===\n";
try {
    $pasiens = DB::table('pasien')->select('id','user_id','no_rm')->get();
    foreach ($pasiens as $p) {
        $noRm = $p->no_rm ?? 'NULL';
        echo "ID:{$p->id} UserID:{$p->user_id} NoRM:{$noRm}\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== BUAT USER PASIEN ===\n";
try {
    $check = DB::table('users')->where('email','demopassien@sahaduta.com')->first();
    if ($check) {
        echo "Sudah ada. ID:{$check->id} Role:{$check->role}\n";
        $uid = $check->id;
    } else {
        $uid = DB::table('users')->insertGetId([
            'name'       => 'Demo Pasien',
            'email'      => 'demopassien@sahaduta.com',
            'password'   => Hash::make('pasien123'),
            'role'       => 'pasien',
            'is_active'  => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "User dibuat. ID:$uid\n";
    }

    echo "\n=== BUAT RECORD PASIEN ===\n";
    $cekPasien = DB::table('pasien')->where('user_id', $uid)->first();
    if ($cekPasien) {
        echo "Record pasien sudah ada. NoRM:{$cekPasien->no_rm}\n";
    } else {
        DB::table('pasien')->insert([
            'user_id'       => $uid,
            'no_rm'         => 'RM-20260319-9001',
            'nama'          => 'Demo Pasien',
            'tgl_lahir'     => '1995-01-01',
            'jenis_kelamin' => 'L',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        echo "Record pasien dibuat. NoRM: RM-20260319-9001\n";
    }
    echo "\nKREDENSIAL:\nNo RM   : RM-20260319-9001\nPassword: pasien123\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "TRACE: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
