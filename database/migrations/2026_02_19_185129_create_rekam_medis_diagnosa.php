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
        Schema::create('rekam_medis_diagnosa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->onDelete('cascade');
            $table->foreignId('icdx_id')->constrained('icdx')->onDelete('cascade');
            $table->boolean('is_primer')->default(false)->comment('true = diagnosa utama, false = diagnosa sekunder');
            $table->timestamps();

            $table->unique(['rekam_medis_id', 'icdx_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rekam_medis_diagnosa');
    }
};
