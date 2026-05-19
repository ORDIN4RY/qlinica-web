<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('billing', function (Blueprint $table) {
            // Menambahkan biaya_kamar setelah biaya_tindakan
            $table->decimal('biaya_kamar', 12, 2)->default(0)->after('biaya_tindakan');
            // Menambahkan foreign key ke rawat_inap_id agar billing ini terhubung ke transaksi rawat inap
            $table->foreignId('rawat_inap_id')->nullable()->after('rekam_medis_id')->constrained('rawat_inap')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('billing', function (Blueprint $table) {
            $table->dropForeign(['rawat_inap_id']);
            $table->dropColumn('rawat_inap_id');
            $table->dropColumn('biaya_kamar');
        });
    }
};
