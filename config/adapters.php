<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Adapter Configuration
    |--------------------------------------------------------------------------
    |
    | Configure notification channels and their settings.
    | Available types: 'http', 'email', 'sms', 'mock'
    |
    | You can configure multiple channels, each with their own settings.
    | Use 'default_channels' to specify which channels to use by default.
    |
    */
    'notification' => [
        'channels' => [
            'email' => [
                'type'   => env('EMAIL_NOTIFICATION_TYPE', 'gmail'),
                'config' => [
                    'url'     => env('EMAIL_NOTIFICATION_URL'),
                    'api_key' => env('EMAIL_NOTIFICATION_API_KEY'),
                    'timeout' => env('EMAIL_NOTIFICATION_TIMEOUT', 10),
                ],
            ],
            'sms' => [
                'type'   => env('SMS_NOTIFICATION_TYPE', 'twilio'),
                'config' => [
                    'url'     => env('SMS_NOTIFICATION_URL'),
                    'api_key' => env('SMS_NOTIFICATION_API_KEY'),
                    'timeout' => env('SMS_NOTIFICATION_TIMEOUT', 10),
                ],
            ],
        ],

        'default_channels' => array_filter(explode(',', env('NOTIFICATION_DEFAULT_CHANNELS', 'email'))),
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
