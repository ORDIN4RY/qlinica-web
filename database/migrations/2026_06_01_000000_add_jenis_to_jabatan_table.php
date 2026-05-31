<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom jenis ke tabel jabatan.
     * medis     = jabatan yang berhubungan langsung dengan pelayanan medis (dokter, perawat, apoteker, dll)
     * non-medis = jabatan administratif / pendukung (admin, satpam, OB, teknisi, dll)
     */
    public function up(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->enum('jenis', ['medis', 'non-medis'])
                  ->default('non-medis')
                  ->after('nama_jabatan')
                  ->comment('Kategori jabatan: medis atau non-medis');
        });
    }

    public function down(): void
    {
        Schema::table('jabatan', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });
    }
};
