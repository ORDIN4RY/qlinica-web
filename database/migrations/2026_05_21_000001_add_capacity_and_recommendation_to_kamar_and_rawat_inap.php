<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('kamar', function (Blueprint $table) {
            $table->integer('kapasitas')->default(1)->after('status');
            $table->integer('terisi')->default(0)->after('kapasitas');
        });
        Schema::table('rawat_inap', function (Blueprint $table) {
            $table->unsignedBigInteger('rekomendasi_kamar_id')->nullable()->after('kamar_id');
            $table->foreign('rekomendasi_kamar_id')->references('id')->on('kamar')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('rawat_inap', function (Blueprint $table) {
            $table->dropForeign(['rekomendasi_kamar_id']);
            $table->dropColumn('rekomendasi_kamar_id');
        });
        Schema::table('kamar', function (Blueprint $table) {
            $table->dropColumn(['kapasitas', 'terisi']);
        });
    }
};
