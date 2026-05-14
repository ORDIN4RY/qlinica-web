<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hak_akses', function (Blueprint $table) {
            $table->json('sub_akses')->nullable()->after('menu_id');
            // Hapus kolom legacy
            $table->dropColumn(['bisa_lihat', 'bisa_tambah', 'bisa_edit', 'bisa_hapus']);
        });
    }

    public function down(): void
    {
        Schema::table('hak_akses', function (Blueprint $table) {
            $table->dropColumn('sub_akses');
            $table->boolean('bisa_lihat')->default(false)->after('menu_id');
            $table->boolean('bisa_tambah')->default(false)->after('bisa_lihat');
            $table->boolean('bisa_edit')->default(false)->after('bisa_tambah');
            $table->boolean('bisa_hapus')->default(false)->after('bisa_edit');
        });
    }
};
