<?php

arch('dtos-naming')
    ->expect('App\Domains\*\DTOs')
    ->classes()
    ->toHaveSuffix('DTO');

arch('services-naming')
    ->expect('App\Domains\*\Services')
    ->classes()
    ->toHaveSuffix('Service');

arch('repositories-naming')
    ->expect('App\Domains\*\Repositories')
    ->classes()
    ->toHaveSuffix('Repository')
    ->ignoring('App\Domains\*\Repositories\Contracts');

arch('repository-interfaces-naming')
    ->expect('App\Domains\*\Repositories\Contracts')
    ->toHaveSuffix('RepositoryInterface');

arch('exceptions-naming')
    ->expect([
        'App\Exceptions',
        'App\Domains\*\Exceptions',
    ])
    ->classes()
    ->toHaveSuffix('Exception');

arch('resources-naming')
    ->expect([
        'App\Http\Resources',
        'App\Domains\*\Resources',
    ])
    ->classes()
    ->toHaveSuffix('Resource');

arch('enums-no-suffix')
    ->expect([
        'App\Enums',
        'App\Domains\*\Enums',
    ])
    ->classes()
    ->not->toHaveSuffix('Enum');

arch('models-no-suffix')
    ->expect([
        'App\Models',
        'App\Domains\*\Models',
    ])
    ->classes()
    ->not->toHaveSuffix('Model');

arch('laravel-service-provider-wrong-suffix')
    ->expect('App')
    ->not->toHaveSuffix('ServiceProvider')
    ->ignoring('App\Providers');

arch('laravel-controllers-wrong-suffix')
    ->expect('App')
    ->not->toHaveSuffix('Controller')
    ->ignoring('App\Http\Controllers');

arch('laravel-controllers-suffix')
    ->expect('App\Http\Controllers')
    ->classes()
    ->toHaveSuffix('Controller');

arch('laravel-policies')
    ->expect([
        'App\Policies',
        'App\Domains\*\Policies',
    ])
    ->classes()
    ->toHaveSuffix('Policy');
