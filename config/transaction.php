<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Transaction Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for transaction processing
    |
    */

    'limits' => [
        'max_transfer_amount'  => env('MAX_TRANSFER_AMOUNT', 100000), // in cents
        'max_deposit_amount'   => env('MAX_DEPOSIT_AMOUNT', 1000000), // in cents
        'daily_transfer_limit' => env('DAILY_TRANSFER_LIMIT', 500000), // in cents
    ],

    'fees' => [
        'transfer_fee_percentage' => env('TRANSFER_FEE_PERCENTAGE', 0.0), // 0% by default
        'deposit_fee_percentage'  => env('DEPOSIT_FEE_PERCENTAGE', 0.0), // 0% by default
    ],

    'validation' => [
        'require_authorization_service' => env('REQUIRE_AUTHORIZATION_SERVICE', true),
        'authorization_service_url'     => env('AUTHORIZATION_SERVICE_URL', 'https://util.devi.tools/api/v2/authorize'),
        'authorization_timeout'         => env('AUTHORIZATION_TIMEOUT', 5), // seconds
    ],
];
