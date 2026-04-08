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
        Schema::table('antrian', function (Blueprint $table) {
            $table->unsignedBigInteger('jadwal_dokter_id')->nullable()->change();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `antrian` MODIFY COLUMN `status` ENUM('Menunggu','Dipanggil','Dilayani','Selesai','Batal') NOT NULL DEFAULT 'Menunggu';");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TYPE status_type RENAME TO status_type_old;");
            DB::statement("CREATE TYPE status_type AS ENUM ('Menunggu','Dipanggil','Dilayani','Selesai','Batal');");
            DB::statement("ALTER TABLE antrian ALTER COLUMN status TYPE status_type USING status::text::status_type;");
            DB::statement("DROP TYPE status_type_old;");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('antrian', function (Blueprint $table) {
            $table->unsignedBigInteger('jadwal_dokter_id')->nullable(false)->change();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `antrian` MODIFY COLUMN `status` ENUM('Menunggu','Dipanggil','Selesai','Batal') NOT NULL DEFAULT 'Menunggu';");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TYPE status_type RENAME TO status_type_old;");
            DB::statement("CREATE TYPE status_type AS ENUM ('Menunggu','Dipanggil','Selesai','Batal');");
            DB::statement("ALTER TABLE antrian ALTER COLUMN status TYPE status_type USING status::text::status_type;");
            DB::statement("DROP TYPE status_type_old;");
        }
    }
};
