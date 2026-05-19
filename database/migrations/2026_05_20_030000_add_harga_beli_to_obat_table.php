<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->decimal('harga_beli', 12, 2)->default(0.00)->after('stok_minimum')->comment('Harga modal dari distributor');
        });

        // Inisialisasi harga_beli untuk obat yang sudah ada (diambil default flat 80% dari harga jual saat ini)
        DB::table('obat')->update([
            'harga_beli' => DB::raw('harga * 0.80')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('obat', function (Blueprint $table) {
            $table->dropColumn('harga_beli');
        });
    }
};
