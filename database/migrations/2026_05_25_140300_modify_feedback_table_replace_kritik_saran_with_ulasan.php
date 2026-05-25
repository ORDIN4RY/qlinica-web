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
        Schema::table('feedback', function (Blueprint $table) {
            // Drop kritik and saran columns
            $table->dropColumn(['kritik', 'saran']);
            // Add ulasan column
            $table->text('ulasan')->nullable()->after('rekam_medis_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            $table->dropColumn('ulasan');
            $table->text('kritik')->nullable()->after('rekam_medis_id');
            $table->text('saran')->nullable()->after('kritik');
        });
    }
};
