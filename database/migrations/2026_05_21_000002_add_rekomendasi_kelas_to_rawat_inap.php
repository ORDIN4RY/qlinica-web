<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('rawat_inap', function (Blueprint $table) {
            $table->string('rekomendasi_kelas')->nullable()->after('rekomendasi_kamar_id');
        });
    }

    public function down()
    {
        Schema::table('rawat_inap', function (Blueprint $table) {
            $table->dropColumn('rekomendasi_kelas');
        });
    }
};
