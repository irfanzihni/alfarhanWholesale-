<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Active Gateway
    |--------------------------------------------------------------------------
    | Supported: "toyyibpay", "billplz"
    */
    'gateway' => env('PAYMENT_GATEWAY', 'toyyibpay'),

    'toyyibpay' => [
        'secret_key' => env('TOYYIBPAY_SECRET_KEY'),
        'category_code' => env('TOYYIBPAY_CATEGORY_CODE'),
        'sandbox' => env('TOYYIBPAY_SANDBOX', true),
        'base_url' => env('TOYYIBPAY_SANDBOX', true)
            ? 'https://dev.toyyibpay.com'
            : 'https://toyyibpay.com',
    ],

    'billplz' => [
        'api_key' => env('BILLPLZ_API_KEY'),
        'collection_id' => env('BILLPLZ_COLLECTION_ID'),
        'x_signature_key' => env('BILLPLZ_X_SIGNATURE_KEY'),
        'sandbox' => env('BILLPLZ_SANDBOX', true),
        'base_url' => env('BILLPLZ_SANDBOX', true)
            ? 'https://www.billplz-sandbox.com/api/v3'
            : 'https://www.billplz.com/api/v3',
    ],

];
