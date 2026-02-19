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
        Schema::create('pasien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('no_rm', 15)->unique()->comment('Format: RM-YYYYMMDD-XXXX, digenerate otomatis');
            $table->string('nik', 16)->unique()->nullable();
            $table->string('nama', 100);
            $table->string('nama_kk', 100)->nullable()->comment('Nama kepala keluarga');
            $table->date('tgl_lahir');
            // 'umur' TIDAK disimpan — hitung dari tgl_lahir: Carbon::parse($tgl_lahir)->age
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('desa', 50)->nullable();
            $table->string('kota', 50)->nullable();
            $table->foreignId('agama_id')->nullable()->constrained('agama')->nullOnDelete();
            $table->foreignId('pendidikan_id')->nullable()->constrained('pendidikan')->nullOnDelete();
            $table->foreignId('pekerjaan_id')->nullable()->constrained('pekerjaan')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
