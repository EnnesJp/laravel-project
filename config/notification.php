<?php

return [

    /*
    |--------------------------------------------------------------------------
    | External Notification Service
    |--------------------------------------------------------------------------
    |
    | Configuration for external notification service integration.
    | This service is called to send notifications about transaction events
    | to external systems or users.
    |
    */

    'service' => [
        'url'     => env('NOTIFICATION_SERVICE_URL'),
        'timeout' => env('NOTIFICATION_SERVICE_TIMEOUT', 10),
    ],

];
