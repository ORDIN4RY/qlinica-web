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
        Schema::create('icdx', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique()->comment('Kode ICD-X, contoh: A00, B01.1');
            $table->string('nama', 255)->comment('Nama diagnosa dalam Bahasa Indonesia');
            $table->string('nama_en', 255)->nullable()->comment('Nama diagnosa dalam Bahasa Inggris');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('icdx');
    }
};
