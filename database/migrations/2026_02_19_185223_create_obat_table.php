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
        Schema::create('obat', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 20)->unique()->nullable();
            $table->string('nama', 100);
            $table->string('satuan', 20)->nullable()->comment('Contoh: tablet, kapsul, ml, botol');
            $table->string('kategori', 50)->nullable()->comment('Contoh: antibiotik, analgesik, vitamin');
            $table->unsignedInteger('stok')->default(0);
            $table->unsignedInteger('stok_minimum')->default(10)->comment('Alert jika stok di bawah nilai ini');
            $table->decimal('harga', 12, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obat');
    }
};
