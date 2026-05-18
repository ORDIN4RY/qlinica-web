<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            // Jatah cuti tahunan per pegawai (default 12 hari sesuai UU Ketenagakerjaan)
            $table->unsignedTinyInteger('jatah_cuti')->default(12)->after('no_hp');
        });
    }

    public function down(): void
    {
        Schema::table('pegawai', function (Blueprint $table) {
            $table->dropColumn('jatah_cuti');
        });
    }
};
