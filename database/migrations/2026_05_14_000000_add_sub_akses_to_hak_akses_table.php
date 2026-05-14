<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hak_akses', function (Blueprint $table) {
            // Kolom JSON fleksibel untuk sub-akses per menu
            // Format: {"view": true, "admin_dashboard": true, "dokter_dashboard": false, ...}
            $table->json('sub_akses')->nullable()->after('menu_id');
        });

        // Migrasi data lama ke format baru (backward-compatible)
        DB::table('hak_akses')->get()->each(function ($row) {
            $sub = [];
            if ($row->bisa_lihat)  $sub['view']   = true;
            if ($row->bisa_tambah) $sub['tambah']  = true;
            if ($row->bisa_edit)   $sub['edit']    = true;
            if ($row->bisa_hapus)  $sub['hapus']   = true;
            DB::table('hak_akses')->where('id', $row->id)->update(['sub_akses' => json_encode($sub)]);
        });
    }

    public function down(): void
    {
        Schema::table('hak_akses', function (Blueprint $table) {
            $table->dropColumn('sub_akses');
        });
    }
};
