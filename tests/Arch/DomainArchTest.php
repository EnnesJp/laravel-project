<?php

arch('transaction-domain-isolation')
    ->expect('App\Domains\Transaction')
    ->not->toUse([
        'App\Domains\User\Repositories',
    ]);

arch('user-domain-isolation')
    ->expect('App\Domains\User')
    ->not->toUse([
        'App\Domains\Transaction\Models',
        'App\Domains\Transaction\Services',
        'App\Domains\Transaction\Repositories',
    ])
    ->ignoring([
        'App\Domains\User\Models',
    ]);

arch('domain-resources-extend')
    ->expect([
        'App\Domains\Transaction\Resources',
        'App\Domains\User\Resources',
    ])
    ->classes()
    ->toExtend('Illuminate\Http\Resources\Json\JsonResource');
