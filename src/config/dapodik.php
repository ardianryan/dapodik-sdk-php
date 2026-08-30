<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dapodik Server Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi host dan port tempat aplikasi Dapodik desktop berjalan
    | di jaringan lokal atau VPS sekolah.
    |
    */

    'host' => env('DAPODIK_HOST', '127.0.0.1'),
    'port' => env('DAPODIK_PORT', 5774),
    'base_url' => env('DAPODIK_BASE_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | NPSN (8 digit) dan Token WebService yang didapatkan dari menu
    | Pengaturan > WebService di aplikasi Dapodik.
    |
    */

    'npsn' => env('DAPODIK_NPSN', ''),
    'token' => env('DAPODIK_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | Batas waktu request ke WebService Dapodik dalam detik.
    |
    */

    'timeout' => env('DAPODIK_TIMEOUT', 30.0),
];
