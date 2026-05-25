<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi Midtrans Payment Gateway.
    | Dapatkan keys dari https://dashboard.midtrans.com
    |
    */

    'server_key'     => env('MIDTRANS_SERVER_KEY', ''),
    'client_key'     => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production'  => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized'   => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds'         => env('MIDTRANS_IS_3DS', true),

    /*
    |--------------------------------------------------------------------------
    | QRIS Settings
    |--------------------------------------------------------------------------
    */
    'qris_expiry_minutes' => env('MIDTRANS_QRIS_EXPIRY', 30),
];
