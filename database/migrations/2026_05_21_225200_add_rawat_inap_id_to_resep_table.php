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
                if (!Schema::hasColumn('resep', 'rawat_inap_id')) {
                    $table->foreignId('rawat_inap_id')->nullable()->after('rekam_medis_id')
                        ->constrained('rawat_inap')->nullOnDelete();
                }
                
                // Allow rekam_medis_id to be nullable so inpatient prescriptions can bypass the unique constraint
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
                if (Schema::hasColumn('resep', 'rawat_inap_id')) {
                    $table->dropForeign(['rawat_inap_id']);
                    $table->dropColumn('rawat_inap_id');
                }
            });
        }
    }
};
