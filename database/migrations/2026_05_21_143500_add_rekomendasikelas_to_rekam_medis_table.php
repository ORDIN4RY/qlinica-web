<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->string('rekomendasi_kelas')->nullable()->after('is_rekomendasi_rawat_inap');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rekam_medis', function (Blueprint $table) {
            $table->dropColumn('rekomendasi_kelas');
        });
    }
};
