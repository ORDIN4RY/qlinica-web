<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom jam_masuk dan jam_keluar ke tabel presensis.
     * Kolom ini dibutuhkan untuk fitur clock-in dan clock-out dari aplikasi mobile.
     */
    public function up(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->time('jam_masuk')->nullable()->after('tanggal');
            $table->time('jam_keluar')->nullable()->after('jam_masuk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropColumn(['jam_masuk', 'jam_keluar']);
        });
    }
};
