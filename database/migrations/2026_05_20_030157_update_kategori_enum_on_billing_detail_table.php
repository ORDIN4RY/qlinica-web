<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE billing_detail MODIFY COLUMN kategori ENUM('Registrasi', 'Tindakan', 'Obat', 'Kamar')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting enum might be risky if there are 'Kamar' records, but here is the statement
        // DB::statement("ALTER TABLE billing_detail MODIFY COLUMN kategori ENUM('Registrasi', 'Tindakan', 'Obat')");
    }
};
