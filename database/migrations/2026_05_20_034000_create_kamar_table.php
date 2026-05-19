<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kamar', 20)->unique();
            $table->string('nama_kamar');
            $table->enum('kelas', ['VIP', 'Kelas 1', 'Kelas 2', 'Kelas 3']);
            $table->decimal('tarif_per_malam', 12, 2);
            $table->enum('status', ['Tersedia', 'Terisi', 'Perbaikan'])->default('Tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamar');
    }
};
