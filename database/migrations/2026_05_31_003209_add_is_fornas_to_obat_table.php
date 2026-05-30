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
        Schema::table('obat', function (Blueprint $table) {
            $table->boolean('is_fornas')->default(false)->after('keterangan');
            $table->string('kode_fornas', 30)->nullable()->after('is_fornas');
            $table->string('kelas_terapi', 100)->nullable()->after('kode_fornas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn(['is_fornas', 'kode_fornas', 'kelas_terapi']);
        });
    }
};
