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
        Schema::create('rawat_inap_kamar_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rawat_inap_id')->constrained('rawat_inap')->onDelete('cascade');
            $table->foreignId('kamar_id')->constrained('kamar')->onDelete('restrict');
            $table->decimal('tarif_per_malam', 12, 2);
            $table->dateTime('tgl_mulai');
            $table->dateTime('tgl_selesai')->nullable();
            $table->timestamps();
        });

        // Seed existing rawat_inap records into the history table
        $rawatInaps = DB::table('rawat_inap')->get();
        foreach ($rawatInaps as $ri) {
            $kamar = DB::table('kamar')->where('id', $ri->kamar_id)->first();
            DB::table('rawat_inap_kamar_history')->insert([
                'rawat_inap_id' => $ri->id,
                'kamar_id' => $ri->kamar_id,
                'tarif_per_malam' => $kamar ? $kamar->tarif_per_malam : 0.00,
                'tgl_mulai' => $ri->tgl_masuk,
                'tgl_selesai' => $ri->status === 'Selesai' ? $ri->tgl_keluar : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rawat_inap_kamar_history');
    }
};

