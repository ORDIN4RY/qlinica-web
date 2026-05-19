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
        Schema::table('billing', function (Blueprint $table) {
            $table->string('no_bpjs', 50)->nullable()->after('no_invoice');
            $table->decimal('potongan_bpjs', 12, 2)->default(0.00)->after('biaya_obat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing', function (Blueprint $table) {
            $table->dropColumn(['no_bpjs', 'potongan_bpjs']);
        });
    }
};
