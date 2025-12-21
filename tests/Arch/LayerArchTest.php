<?php

arch('controllers-use-services')
    ->expect('App\Http\Controllers')
    ->toUse('App\Domains\*\Services');

arch('controllers-not-use-repositories')
    ->expect('App\Http\Controllers')
    ->not->toUse('App\Domains\*\Repositories');

arch('controllers-not-use-models-directly')
    ->expect('App\Http\Controllers')
    ->not->toUse('App\Domains\*\Models');

arch('repositories-use-models')
    ->expect('App\Domains\*\Repositories')
    ->toUse('App\Domains\*\Models');

arch('repositories-use-dtos')
    ->expect('App\Domains\*\Repositories')
    ->toUse('App\Domains\*\DTOs');
