<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom hari_libur ke tabel pegawai.
     * 0 = Minggu, 1 = Senin, 2 = Selasa, 3 = Rabu,
     * 4 = Kamis, 5 = Jumat, 6 = Sabtu
     */
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            // Default 0 = Minggu (hari libur umum)
            $table->tinyInteger('hari_libur')->default(0)->after('jatah_cuti')
                ->comment('0=Minggu, 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn('hari_libur');
        });
    }
};
