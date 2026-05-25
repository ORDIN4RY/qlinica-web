<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MidtransService;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    /**
     * Handle notifikasi webhook dari Midtrans.
     * URL ini harus didaftarkan di Midtrans Dashboard:
     * Settings → Configuration → Payment Notification URL
     *
     * URL: https://yourdomain.com/api/midtrans/webhook
     */
    public function handle(Request $request)
    {
        Log::info('Midtrans webhook hit', ['payload' => $request->all()]);

        $midtrans = new MidtransService();
        $result   = $midtrans->handleWebhook();

        return response()->json($result, $result['success'] ? 200 : 400);
    }
}
