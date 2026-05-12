<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan kolom-kolom pelayanan klinis yang dibutuhkan form diagnosa dokter
     * tetapi belum ada di migration awal rekam_medis.
     */
    public function up(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->string('kasus_penyakit', 100)->nullable()->after('catatan')
                ->comment('Baru / Lama / KKL');
            $table->string('pelayanan_kesehatan', 100)->nullable()->after('kasus_penyakit')
                ->comment('BPJS / UMUM');
            $table->string('status_pasien', 100)->nullable()->after('pelayanan_kesehatan')
                ->comment('Baru / Lama');
            $table->string('jenis_pelayanan', 100)->nullable()->after('status_pasien')
                ->comment('Poli Umum / Poli Gigi / UGD / dll');
            $table->text('pengobatan')->nullable()->after('jenis_pelayanan')
                ->comment('Rencana pengobatan / terapi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropColumn([
                'kasus_penyakit',
                'pelayanan_kesehatan',
                'status_pasien',
                'jenis_pelayanan',
                'pengobatan',
            ]);
        });
    }
};
