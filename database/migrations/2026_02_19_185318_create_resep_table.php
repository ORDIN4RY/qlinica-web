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
        Schema::create('resep', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->unique()->constrained('rekam_medis')->onDelete('cascade');
            $table->foreignId('dokter_id')->constrained('pegawai')->onDelete('cascade');
            $table->foreignId('apoteker_id')->nullable()->constrained('pegawai')->nullOnDelete()
                ->comment('Diisi oleh apoteker yang memproses resep');
            $table->enum('status', ['Menunggu', 'Diproses', 'Selesai', 'Dibatalkan'])->default('Menunggu');
            $table->text('catatan_dokter')->nullable();
            $table->text('catatan_apoteker')->nullable();
            $table->timestamp('diproses_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};
