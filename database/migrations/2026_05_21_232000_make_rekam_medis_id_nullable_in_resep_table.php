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
        if (Schema::hasTable('resep')) {
            Schema::table('resep', function (Blueprint $table) {
                // Modifikasi kolom rekam_medis_id agar bisa menerima nilai NULL (untuk resep rawat inap harian)
                $table->foreignId('rekam_medis_id')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('resep')) {
            Schema::table('resep', function (Blueprint $table) {
                // Kembalikan ke NOT NULL jika di-rollback
                $table->foreignId('rekam_medis_id')->nullable(false)->change();
            });
        }
    }
};
