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
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE billing_detail MODIFY COLUMN kategori ENUM('Registrasi', 'Tindakan', 'Obat', 'Kamar')");
        } elseif ($driver === 'pgsql') {
            // PostgreSQL does not support MODIFY COLUMN / ENUM() syntax.
            // We cast the column to VARCHAR first, then apply a CHECK constraint.
            DB::statement("ALTER TABLE billing_detail ALTER COLUMN kategori TYPE VARCHAR(50) USING kategori::VARCHAR(50)");
            DB::statement("ALTER TABLE billing_detail DROP CONSTRAINT IF EXISTS billing_detail_kategori_check");
            DB::statement("ALTER TABLE billing_detail ADD CONSTRAINT billing_detail_kategori_check CHECK (kategori IN ('Registrasi', 'Tindakan', 'Obat', 'Kamar'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE billing_detail MODIFY COLUMN kategori ENUM('Registrasi', 'Tindakan', 'Obat')");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE billing_detail DROP CONSTRAINT IF EXISTS billing_detail_kategori_check");
            DB::statement("ALTER TABLE billing_detail ADD CONSTRAINT billing_detail_kategori_check CHECK (kategori IN ('Registrasi', 'Tindakan', 'Obat'))");
        }
    }
};
