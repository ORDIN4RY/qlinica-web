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
        Schema::create('resep_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('resep')->onDelete('cascade');
            $table->foreignId('obat_id')->constrained('obat')->onDelete('cascade');
            $table->unsignedInteger('jumlah')->comment('Jumlah obat yang diresepkan');
            $table->string('dosis', 100)->nullable()->comment('Contoh: 3x1 setelah makan, 2x1/2 pagi dan malam');
            $table->string('aturan_pakai', 100)->nullable()->comment('Contoh: Sesudah makan, Sebelum tidur');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resep_detail');
    }
};
