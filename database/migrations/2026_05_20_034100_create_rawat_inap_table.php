<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rawat_inap', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->foreignId('kamar_id')->constrained('kamar')->onDelete('restrict');
            $table->foreignId('dokter_id')->constrained('pegawai')->onDelete('restrict');
            $table->enum('jenis_penjamin', ['Umum', 'BPJS KESEHATAN', 'Asuransi Lain'])->default('Umum');
            $table->string('no_sep', 50)->nullable();
            $table->dateTime('tgl_masuk');
            $table->dateTime('tgl_keluar')->nullable();
            $table->enum('status', ['Aktif', 'Selesai'])->default('Aktif');
            $table->text('catatan_keluar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rawat_inap');
    }
};
