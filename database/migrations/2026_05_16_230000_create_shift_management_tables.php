<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Master Shift
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('nama_shift'); // Pagi, Sore, Malam, dsb
            $table->time('jam_masuk');
            $table->time('jam_pulang');
            $table->timestamps();
        });

        // 2. Tabel Jadwal Shift Pegawai
        Schema::create('jadwal_shifts', function (Blueprint $table) {
            $table->id();
            // Perbaikan: Nama tabel adalah 'pegawai' (tanpa s)
            $table->foreignId('pegawai_id')->constrained('pegawai')->onDelete('cascade');
            $table->foreignId('shift_id')->constrained('shifts')->onDelete('cascade');
            $table->date('tanggal');
            $table->timestamps();

            $table->unique(['pegawai_id', 'tanggal']);
        });

        // 3. Tambahkan link ke presensi
        Schema::table('presensis', function (Blueprint $table) {
            $table->foreignId('jadwal_shift_id')->nullable()->after('pegawai_id')->constrained('jadwal_shifts')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('presensis', function (Blueprint $table) {
            $table->dropForeign(['jadwal_shift_id']);
            $table->dropColumn('jadwal_shift_id');
        });
        Schema::dropIfExists('jadwal_shifts');
        Schema::dropIfExists('shifts');
    }
};
