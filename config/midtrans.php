<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Midtrans Merchant
    |--------------------------------------------------------------------------
    |
    | Merchant ID untuk memverifikasi callback Midtrans.
    |
    */

    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Client Key
    |--------------------------------------------------------------------------
    |
    | Kunci publik untuk Snap.js di sisi frontend. Aman untuk diekspos.
    |
    */

    'client_key' => env('MIDTRANS_CLIENT_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Server Key
    |--------------------------------------------------------------------------
    |
    | Kunci rahasia untuk Autentikasi API dan verifikasi Signature Key
    | webhook. JANGAN pernah diekspos ke frontend.
    |
    */

    'server_key' => env('MIDTRANS_SERVER_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | false = Sandbox, true = Production.
    |
    */

    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    |--------------------------------------------------------------------------
    | Sanitize & 3DS
    |--------------------------------------------------------------------------
    |
    | Sanitize memvalidasi field yang dikirim ke Midtrans, sementara 3DS
    | mengaktifkan proteksi 3-D Secure untuk transaksi kartu kredit.
    |
    */

    'is_sanitized' => true,
    'is_3ds' => true,

];
