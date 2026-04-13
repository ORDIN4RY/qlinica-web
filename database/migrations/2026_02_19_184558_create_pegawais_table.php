<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawai', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('nik', 20)->unique()->nullable();
            $table->string('nama', 100);
            $table->string('spesialisasi', 100)->nullable()->comment('Diisi khusus untuk dokter');
            $table->string('no_sip', 60)->nullable()->comment('Nomor Surat Izin Praktik dokter');
            $table->text('alamat')->nullable();
            $table->string('no_hp', 15)->nullable();
            $table->text('foto')->nullable()->comment('Path atau URL foto profil pegawai');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawai');
    }
};
