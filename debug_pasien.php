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
            'no_rm'         => 'RM-260525-001',
            'nama'          => 'Demo Pasien',
            'tgl_lahir'     => '1995-01-01',
            'jenis_kelamin' => 'L',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);
        echo "Record pasien dibuat. NoRM: RM-20260319-9001\n";
    }
    echo "\nKREDENSIAL:\nNo RM   : RM-260525-001\nPassword: pasien123\n";

    echo "\n=== DETAIL RAWAT INAP & BILLING ===\n";
    $pasiens = DB::table('pasien')->get();
    foreach ($pasiens as $p) {
        $rawatInapAktif = DB::table('rawat_inap')->where('pasien_id', $p->id)->whereNull('tgl_keluar')->get();
        $billingBelumLunas = DB::table('billing')->where('pasien_id', $p->id)->where('status', 'Belum Bayar')->get();
        
        echo "Pasien ID: {$p->id}, Nama: {$p->nama}, NoRM: {$p->no_rm}\n";
        echo "  - Rawat Inap Aktif: " . $rawatInapAktif->count() . "\n";
        foreach ($rawatInapAktif as $ri) {
            echo "    * ID: {$ri->id}, Status: " . ($ri->status ?? 'NULL') . ", Tgl Masuk: {$ri->tgl_masuk}\n";
        }
        echo "  - Billing Belum Lunas: " . $billingBelumLunas->count() . "\n";
        foreach ($billingBelumLunas as $b) {
            echo "    * Invoice: {$b->no_invoice}, Grand Total: {$b->grand_total}, Status: {$b->status}, Rawat Inap ID: " . ($b->rawat_inap_id ?? 'NULL') . "\n";
        }
    }
    echo "\n=== DETAIL ANTRIAN PASIEN 1 ===\n";
    $antrians = DB::table('antrian')->where('pasien_id', 1)->get();
    echo "Total Antrian: " . $antrians->count() . "\n";
    foreach ($antrians as $a) {
        echo "  - ID: {$a->id}, No: {$a->no_antrian}, Tanggal: {$a->tanggal}, Status: {$a->status}, Jenis: {$a->jenis}\n";
    }

    echo "\n=== TESTING NEWLY CREATED ANTRIAN MODEL ===\n";
    $antrian = \App\Models\Antrian::create([
        'no_antrian' => 999,
        'pasien_id'  => 1,
        'jenis'      => 'Online',
        'keluhan'    => 'Test',
        'status'     => 'Menunggu',
        'tanggal'    => now()->toDateString(),
    ]);
    echo "Is Carbon? " . ($antrian->tanggal instanceof \Carbon\Carbon ? 'YES' : 'NO') . "\n";
    echo "Type of tanggal: " . (is_object($antrian->tanggal) ? get_class($antrian->tanggal) : gettype($antrian->tanggal)) . "\n";
    try {
        echo "Formatted: " . $antrian->tanggal->format('d M Y') . "\n";
    } catch (\Error $e) {
        echo "ERROR on format: " . $e->getMessage() . "\n";
    }
    $antrian->forceDelete();

    echo "\n=== SIMULATING TAKE QUEUE FOR PASIEN 1 ===\n";
    $pasien = \App\Models\Pasien::find(1);
    $user = $pasien->user;
    auth()->login($user);

    $request = new \Illuminate\Http\Request();
    $request->merge(['jenis' => 'Online', 'keluhan' => 'Demam']);

    $controller = new \App\Http\Controllers\AntrianController();
    $response = $controller->storePasien($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "TRACE: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
