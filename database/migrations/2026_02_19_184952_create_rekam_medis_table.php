<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('antrian_id')->unique()->constrained('antrian')->onDelete('cascade');
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('pegawai')->onDelete('cascade');
            $table->datetime('tanggal_periksa');

            // Pemeriksaan subjektif
            $table->text('anamnesis')->nullable()->comment('Keluhan utama & riwayat penyakit');

            // Pemeriksaan objektif / vital sign
            $table->text('pemeriksaan_fisik')->nullable();
            $table->string('tekanan_darah', 20)->nullable()->comment('Contoh: 120/80 mmHg');
            $table->decimal('suhu', 4, 1)->nullable()->comment('Dalam Celsius');
            $table->decimal('berat_badan', 5, 2)->nullable()->comment('Dalam kg');
            $table->decimal('tinggi_badan', 5, 2)->nullable()->comment('Dalam cm');
            $table->unsignedInteger('nadi')->nullable()->comment('Denyut nadi per menit');
            $table->unsignedInteger('respirasi')->nullable()->comment('Pernapasan per menit');

            // Assessment & Plan
            $table->text('tindakan')->nullable();
            $table->text('prognosa')->nullable();
            $table->enum('keadaan_keluar', [
                'Sembuh', 'Membaik', 'Belum Sembuh', 'Meninggal', 'Dirujuk'
            ])->nullable();
            $table->string('rujukan_ke', 100)->nullable()->comment('Diisi jika pasien dirujuk');
            $table->text('catatan')->nullable()->comment('Catatan tambahan dokter');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
