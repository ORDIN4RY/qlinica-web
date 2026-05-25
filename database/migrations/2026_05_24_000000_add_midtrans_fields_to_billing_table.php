<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menambah kolom Midtrans ke tabel billing untuk menyimpan
     * informasi transaksi QRIS dari Midtrans Payment Gateway.
     */
    public function up(): void
    {
        Schema::table('billing', function (Blueprint $table) {
            $table->string('midtrans_order_id')->nullable()->after('paid_at')
                  ->comment('Order ID unik yang dikirim ke Midtrans');
            $table->string('midtrans_transaction_id')->nullable()->after('midtrans_order_id')
                  ->comment('Transaction ID dari response Midtrans');
            $table->text('midtrans_qr_url')->nullable()->after('midtrans_transaction_id')
                  ->comment('URL gambar QR Code dari Midtrans');
            $table->string('midtrans_status')->nullable()->after('midtrans_qr_url')
                  ->comment('Status transaksi dari Midtrans: pending/settlement/expire/cancel');
            $table->timestamp('midtrans_qr_generated_at')->nullable()->after('midtrans_status')
                  ->comment('Waktu QR Code di-generate, untuk hitung expiry');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('billing', function (Blueprint $table) {
            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_transaction_id',
                'midtrans_qr_url',
                'midtrans_status',
                'midtrans_qr_generated_at',
            ]);
        });
    }
};
