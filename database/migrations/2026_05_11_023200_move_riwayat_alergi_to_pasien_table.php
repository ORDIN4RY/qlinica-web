<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('rekam_medis', 'riwayat_alergi')) {
            Schema::table('rekam_medis', function (Blueprint $table) {
                $table->dropColumn('riwayat_alergi');
            });
        }

        if (!Schema::hasColumn('pasien', 'riwayat_alergi')) {
            Schema::table('pasien', function (Blueprint $table) {
                $table->text('riwayat_alergi')->nullable()->after('golongan_darah');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pasien', 'riwayat_alergi')) {
            Schema::table('pasien', function (Blueprint $table) {
                $table->dropColumn('riwayat_alergi');
            });
        }

        if (!Schema::hasColumn('rekam_medis', 'riwayat_alergi')) {
            Schema::table('rekam_medis', function (Blueprint $table) {
                $table->text('riwayat_alergi')->nullable()->after('pengobatan');
            });
        }
    }
};
