<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use App\Models\Pasien;
use App\Models\RawatInap;
use App\Models\Antrian;
use App\Models\Billing;
use Carbon\Carbon;

try {
    $pasien = Pasien::find(1);
    if (!$pasien) { echo "Pasien not found\n"; exit; }
    echo "Pasien: " . $pasien->nama . "\n";

    $today = Carbon::today();

    // Test antrian query
    $antrianAktif = Antrian::where('pasien_id', $pasien->id)
        ->where('tanggal', $today)
        ->whereNotIn('status', ['Selesai', 'Batal'])
        ->first();
    echo "AntrianAktif: " . ($antrianAktif ? $antrianAktif->id : 'null') . "\n";

    // Test rawat inap query (fixed column name)
    $rawatInapAktif = RawatInap::where('pasien_id', $pasien->id)
        ->whereNull('tgl_keluar')
        ->first();
    echo "RawatInapAktif: " . ($rawatInapAktif ? $rawatInapAktif->id : 'null') . "\n";

    // Test riwayat antrian
    $riwayat = Antrian::with([
        'pasien',
        'rekamMedis.dokter',
        'rekamMedis.diagnosa.icdx',
        'rekamMedis.resep.details.obat',
    ])
        ->where('pasien_id', $pasien->id)
        ->where('tanggal', '<', $today)
        ->latest('tanggal')
        ->limit(5)
        ->get();
    echo "Riwayat count: " . $riwayat->count() . "\n";

    echo "ALL QUERIES OK\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
