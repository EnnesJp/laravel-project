<?php

arch('enums')
    ->expect('App\Enums')
    ->toBeEnums();

arch('transaction-enums')
    ->expect('App\Domains\*\Enums')
    ->toBeEnums();

arch('laravel-enums')
    ->expect('App')
    ->not->toBeEnums()
    ->ignoring([
        'App\Enums',
        'App\Domains\Transaction\Enums',
        'App\Domains\User\Enums',
    ]);
