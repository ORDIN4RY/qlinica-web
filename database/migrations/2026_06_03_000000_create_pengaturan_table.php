<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Seed nilai default lokasi klinik dan shift
        DB::table('pengaturan')->insert([
            ['key' => 'lokasi_lat',    'value' => '-8.164423',  'created_at' => now(), 'updated_at' => now()],
            ['key' => 'lokasi_lng',    'value' => '113.709018', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'lokasi_radius', 'value' => '100',        'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan');
    }
};
