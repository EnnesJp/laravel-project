<?php

arch('repositories-interface')
    ->expect([
        'App\Repositories\Contracts',
        'App\Domains\*\Repositories\Contracts',
    ])
    ->toBeInterfaces();
