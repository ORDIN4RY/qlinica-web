<?php

namespace App\Services;

use App\Models\Billing;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    /**
     * Generate QRIS QR Code via Midtrans Charge API.
     *
     * @param  Billing $billing
     * @return array ['success' => bool, 'qr_url' => string|null, 'message' => string]
     */
    public function chargeQris(Billing $billing): array
    {
        // Jangan proses jika grand_total = 0 (misal pasien BPJS full-cover)
        if ($billing->grand_total <= 0) {
            return [
                'success' => false,
                'message' => 'Grand total Rp 0 tidak perlu diproses melalui payment gateway.',
                'qr_url'  => null,
            ];
        }

        // Pastikan minimal Rp 100
        if ($billing->grand_total < 100) {
            return [
                'success' => false,
                'message' => 'Nominal terlalu kecil. Minimum pembayaran QRIS adalah Rp 100.',
                'qr_url'  => null,
            ];
        }

        // Buat order ID unik agar tidak konflik jika di-generate ulang
        $orderId = 'QLINICA-' . strtoupper(str_replace('/', '-', $billing->no_invoice))
                 . '-' . now()->format('YmdHis');

        $expiryMinutes = config('midtrans.qris_expiry_minutes', 30);

        $params = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $billing->grand_total,
            ],
            // Tidak perlu tentukan acquirer — Midtrans otomatis pilih berdasarkan yang aktif di akun
            'item_details' => $this->buildItemDetails($billing),
            'customer_details' => [
                'first_name' => $billing->pasien?->nama ?? 'Pasien',
                'email'      => $billing->pasien?->user?->email ?? 'pasien@qlinica.com',
                'phone'      => $billing->pasien?->no_hp ?? '08000000000',
            ],
            'expiry' => [
                'start_time' => now()->format('Y-m-d H:i:s O'),
                'unit'       => 'minutes',
                'duration'   => $expiryMinutes,
            ],
        ];

        try {
            $response = CoreApi::charge($params);

            if (isset($response->transaction_id)) {
                // Ambil URL QR Code
                $qrUrl = null;
                if (!empty($response->actions)) {
                    foreach ($response->actions as $action) {
                        if ($action->name === 'generate-qr-code') {
                            $qrUrl = $action->url;
                            break;
                        }
                    }
                }
                // Fallback: pakai qr_string jika ada
                $qrString = $response->qr_string ?? null;

                // Simpan ke database
                $billing->update([
                    'midtrans_order_id'        => $orderId,
                    'midtrans_transaction_id'  => $response->transaction_id,
                    'midtrans_qr_url'          => $qrUrl,
                    'midtrans_status'          => $response->transaction_status ?? 'pending',
                    'midtrans_qr_generated_at' => now(),
                ]);

                Log::info('Midtrans QRIS charged', [
                    'billing_id' => $billing->id,
                    'order_id'   => $orderId,
                    'status'     => $response->transaction_status,
                ]);

                return [
                    'success'   => true,
                    'qr_url'    => $qrUrl,
                    'qr_string' => $qrString,
                    'order_id'  => $orderId,
                    'message'   => 'QR Code berhasil di-generate. Silakan scan untuk membayar.',
                ];
            }

            return [
                'success' => false,
                'message' => 'Gagal mendapatkan QR Code dari Midtrans. Coba lagi.',
                'qr_url'  => null,
            ];

        } catch (\Exception $e) {
            Log::error('Midtrans QRIS charge error', [
                'billing_id' => $billing->id,
                'error'      => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Error Midtrans: ' . $e->getMessage(),
                'qr_url'  => null,
            ];
        }
    }

    /**
     * Cek status transaksi QRIS dari Midtrans.
     *
     * @param  Billing $billing
     * @return array
     */
    public function checkStatus(Billing $billing): array
    {
        if (!$billing->midtrans_order_id) {
            return ['status' => 'no_transaction', 'message' => 'Belum ada transaksi QRIS.'];
        }

        try {
            $status = \Midtrans\Transaction::status($billing->midtrans_order_id);

            $transactionStatus = $status->transaction_status ?? 'unknown';

            // Update status di database
            $billing->update(['midtrans_status' => $transactionStatus]);

            // Jika settlement (lunas), mark billing sebagai Lunas
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                if ($billing->status !== 'Lunas') {
                    $this->markAsPaid($billing, $status->transaction_id ?? null);
                }
                return ['status' => 'settlement', 'message' => 'Pembayaran berhasil diterima!'];
            }

            if ($transactionStatus === 'pending') {
                return ['status' => 'pending', 'message' => 'Menunggu pembayaran dari pasien...'];
            }

            if (in_array($transactionStatus, ['expire', 'cancel', 'deny'])) {
                return ['status' => $transactionStatus, 'message' => 'Transaksi ' . $transactionStatus . '. QR tidak berlaku lagi.'];
            }

            return ['status' => $transactionStatus, 'message' => 'Status: ' . $transactionStatus];

        } catch (\Exception $e) {
            Log::error('Midtrans status check error', [
                'billing_id' => $billing->id,
                'error'      => $e->getMessage(),
            ]);

            return ['status' => 'error', 'message' => 'Gagal cek status: ' . $e->getMessage()];
        }
    }

    /**
     * Handle webhook notification dari Midtrans.
     *
     * @return array
     */
    public function handleWebhook(): array
    {
        try {
            $notification = new Notification();

            $transactionStatus = $notification->transaction_status;
            $orderId           = $notification->order_id;
            $fraudStatus       = $notification->fraud_status ?? null;

            Log::info('Midtrans webhook received', [
                'order_id'  => $orderId,
                'status'    => $transactionStatus,
                'fraud'     => $fraudStatus,
            ]);

            // Cari billing berdasarkan midtrans_order_id
            $billing = Billing::where('midtrans_order_id', $orderId)->first();

            if (!$billing) {
                Log::warning('Midtrans webhook: billing tidak ditemukan', ['order_id' => $orderId]);
                return ['success' => false, 'message' => 'Billing tidak ditemukan'];
            }

            $billing->update(['midtrans_status' => $transactionStatus]);

            if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
                if ($fraudStatus === null || $fraudStatus === 'accept') {
                    $this->markAsPaid($billing, $notification->transaction_id ?? null);
                    return ['success' => true, 'message' => 'Billing ditandai Lunas'];
                }
            }

            if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                Log::info('Midtrans transaksi gagal/expire', ['order_id' => $orderId]);
            }

            return ['success' => true, 'message' => 'Webhook diproses'];

        } catch (\Exception $e) {
            Log::error('Midtrans webhook error', ['error' => $e->getMessage()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Tandai billing sebagai Lunas setelah pembayaran QRIS berhasil.
     */
    private function markAsPaid(Billing $billing, ?string $transactionId = null): void
    {
        if ($billing->status === 'Lunas') {
            return; // Idempotent — hindari double-update
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($billing, $transactionId) {
            $billing->update([
                'status'                  => 'Lunas',
                'metode_pembayaran'       => 'QRIS',
                'paid_at'                => now(),
                'midtrans_transaction_id' => $transactionId ?? $billing->midtrans_transaction_id,
                'midtrans_status'         => 'settlement',
            ]);

            // Update resep jika ada
            $resep = \App\Models\Resep::where('rekam_medis_id', $billing->rekam_medis_id)->first();
            if ($resep && in_array($resep->status, ['Menunggu Pembayaran', 'Menunggu'])) {
                $resep->update(['status' => 'Sudah Dibayar']);
            }
        });

        Log::info('Billing ditandai Lunas via QRIS Midtrans', [
            'billing_id' => $billing->id,
            'no_invoice' => $billing->no_invoice,
        ]);
    }

    /**
     * Build item details untuk dikirim ke Midtrans.
     */
    private function buildItemDetails(Billing $billing): array
    {
        $items = [];

        if ($billing->biaya_registrasi > 0) {
            $items[] = [
                'id'       => 'REG',
                'price'    => (int) $billing->biaya_registrasi,
                'quantity' => 1,
                'name'     => 'Biaya Registrasi',
            ];
        }

        if ($billing->biaya_tindakan > 0) {
            $items[] = [
                'id'       => 'TDK',
                'price'    => (int) $billing->biaya_tindakan,
                'quantity' => 1,
                'name'     => 'Biaya Tindakan Medis',
            ];
        }

        if ($billing->biaya_obat > 0) {
            $items[] = [
                'id'       => 'OBT',
                'price'    => (int) $billing->biaya_obat,
                'quantity' => 1,
                'name'     => 'Biaya Obat-obatan',
            ];
        }

        if ($billing->biaya_kamar > 0) {
            $items[] = [
                'id'       => 'KMR',
                'price'    => (int) $billing->biaya_kamar,
                'quantity' => 1,
                'name'     => 'Biaya Kamar Rawat Inap',
            ];
        }

        if ($billing->potongan_bpjs > 0) {
            $items[] = [
                'id'       => 'BPJS',
                'price'    => -(int) $billing->potongan_bpjs,
                'quantity' => 1,
                'name'     => 'Potongan BPJS',
            ];
        }

        // Fallback jika tidak ada item tapi ada grand total
        if (empty($items)) {
            $items[] = [
                'id'       => 'INV',
                'price'    => (int) $billing->grand_total,
                'quantity' => 1,
                'name'     => 'Tagihan Klinik ' . $billing->no_invoice,
            ];
        }

        return $items;
    }
}
