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
            $table->decimal('jumlah_dibayar', 12, 2)->nullable()->default(0.00)->after('paid_at');
            $table->decimal('kembalian', 12, 2)->nullable()->default(0.00)->after('jumlah_dibayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing', function (Blueprint $table) {
            $table->dropColumn(['jumlah_dibayar', 'kembalian']);
        });
    }
};
