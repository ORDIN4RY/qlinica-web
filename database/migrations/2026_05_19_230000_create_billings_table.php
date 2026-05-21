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
        Schema::create('billing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekam_medis_id')->constrained('rekam_medis')->onDelete('cascade');
            $table->foreignId('pasien_id')->constrained('pasien')->onDelete('cascade');
            $table->string('no_invoice', 50)->unique();
            $table->decimal('biaya_registrasi', 12, 2)->default(0.00);
            $table->decimal('biaya_tindakan', 12, 2)->default(0.00);
            $table->decimal('biaya_obat', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->enum('status', ['Belum Bayar', 'Lunas', 'Batal'])->default('Belum Bayar');
            $table->string('metode_pembayaran', 50)->nullable();
            $table->foreignId('kasir_id')->nullable()->constrained('pegawai')->onDelete('set null');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });

        Schema::create('billing_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('billing_id')->constrained('billing')->onDelete('cascade');
            $table->string('nama_item', 150);
            $table->enum('kategori', ['Registrasi', 'Tindakan', 'Obat', 'Kamar']);
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 12, 2)->default(0.00);
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->timestamps();
        });

        // Ubah tipe kolom status di tabel resep menjadi string agar menampung 'Menunggu Pembayaran' dan 'Sudah Dibayar'
        Schema::table('resep', function (Blueprint $table) {
            $table->string('status', 50)->default('Menunggu')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan tipe kolom status ke enum awal
        Schema::table('resep', function (Blueprint $table) {
            $table->enum('status', ['Menunggu', 'Diproses', 'Selesai', 'Dibatalkan'])->default('Menunggu')->change();
        });

        Schema::dropIfExists('billing_detail');
        Schema::dropIfExists('billing');
    }
};
