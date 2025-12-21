<?php

return [

    /*
    |--------------------------------------------------------------------------
    | External Validation Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for external validation service integration.
    | This service is called before completing transfers to provide
    | additional security and compliance checking.
    |
    */

    'service' => [
        'url'     => env('EXTERNAL_VALIDATION_SERVICE_URL'),
        'timeout' => env('EXTERNAL_VALIDATION_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configure retry behavior for external validation calls
    |
    */

    'retry' => [
        'attempts' => env('EXTERNAL_VALIDATION_RETRY_ATTEMPTS', 3),
        'delay'    => env('EXTERNAL_VALIDATION_RETRY_DELAY', 1000),
    ],

];
