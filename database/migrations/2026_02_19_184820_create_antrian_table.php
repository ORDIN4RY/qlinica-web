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
        Schema::create('antrian', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('no_antrian')->comment('Nomor urut antrian per dokter per tanggal');
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->foreignId('jadwal_dokter_id')->constrained('jadwal_dokter')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('jenis', ['Online', 'Offline'])->default('Online');
            $table->text('keluhan')->nullable()->comment('Keluhan awal pasien saat mendaftar');
            $table->enum('status', ['Menunggu', 'Dipanggil', 'Selesai', 'Batal'])->default('Menunggu');
            $table->timestamps();
            $table->softDeletes();

            // Nomor antrian unik per dokter per tanggal
            $table->unique(['jadwal_dokter_id', 'tanggal', 'no_antrian']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('antrian');
    }
};
