<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambahkan nilai 'Menunggu Pembayaran' dan 'Sudah Dibayar' ke CHECK constraint
     * kolom status pada tabel resep (diperlukan untuk PostgreSQL).
     */
    public function up(): void
    {
        // Di PostgreSQL, enum diimplementasikan sebagai CHECK constraint.
        // Kita perlu drop constraint lama dan buat yang baru.
        DB::statement('ALTER TABLE resep DROP CONSTRAINT IF EXISTS resep_status_check');
        DB::statement("ALTER TABLE resep ADD CONSTRAINT resep_status_check CHECK (status IN ('Menunggu', 'Diproses', 'Menunggu Pembayaran', 'Sudah Dibayar', 'Selesai', 'Dibatalkan'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE resep DROP CONSTRAINT IF EXISTS resep_status_check');
        DB::statement("ALTER TABLE resep ADD CONSTRAINT resep_status_check CHECK (status IN ('Menunggu', 'Diproses', 'Selesai', 'Dibatalkan'))");
    }
};
