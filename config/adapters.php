<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Adapter Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which notification adapter to use and its settings.
    | Available types: 'http', 'log', 'null'
    |
    */
    'notification' => [
        'type'   => env('NOTIFICATION_ADAPTER_TYPE', 'http'),
        'config' => [
            'url'     => env('NOTIFICATION_SERVICE_URL'),
            'timeout' => env('NOTIFICATION_SERVICE_TIMEOUT', 10),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Adapter Configuration
    |--------------------------------------------------------------------------
    |
    | Configure which validation adapter to use and its settings.
    | Available types: 'http', 'mock', 'null'
    |
    */
    'validation' => [
        'type'   => env('VALIDATION_ADAPTER_TYPE', 'http'),
        'config' => [
            'url'         => env('EXTERNAL_VALIDATION_SERVICE_URL'),
            'timeout'     => env('EXTERNAL_VALIDATION_TIMEOUT', 10),
            'should_pass' => env('VALIDATION_MOCK_SHOULD_PASS', true),
        ],
    ],
];
