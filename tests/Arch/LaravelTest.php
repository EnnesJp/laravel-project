<?php

arch('laravel-trait')
    ->expect('App\Traits')
    ->toBeTraits();

arch('laravel-concerns')
    ->expect('App\Concerns')
    ->toBeTraits();

arch('laravel-throwable')
    ->expect('App')
    ->not->toImplement(Throwable::class)
    ->ignoring([
        'App\Exceptions',
        'App\Domains\Transaction\Exceptions',
        'App\Domains\User\Exceptions',
    ]);

arch('laravel-middleware')
    ->expect('App\Http\Middleware')
    ->classes()
    ->toHaveMethod('handle');

arch('laravel-models')
    ->expect('App\Models')
    ->classes()
    ->toExtend('Illuminate\Database\Eloquent\Model');

arch('domain-models')
    ->expect('App\Domains\*\Models')
    ->classes()
    ->toExtend('Illuminate\Database\Eloquent\Model');

arch('laravel-model-extends')
    ->expect('App')
    ->not->toExtend('Illuminate\Database\Eloquent\Model')
    ->ignoring([
        'App\Models',
        'App\Domains\Transaction\Models',
        'App\Domains\User\Models',
    ]);

arch('laravel-requests-extends')
    ->expect('App\Http\Requests')
    ->toExtend('Illuminate\Foundation\Http\FormRequest');

arch('laravel-requests-rules')
    ->expect('App\Http\Requests')
    ->toHaveMethod('rules');

arch('laravel-form-requests')
    ->expect('App')
    ->not->toExtend('Illuminate\Foundation\Http\FormRequest')
    ->ignoring('App\Http\Requests');

arch('laravel-commands-extends')
    ->expect('App\Console\Commands')
    ->classes()
    ->toExtend('Illuminate\Console\Command');

arch('laravel-commands-handle')
    ->expect('App\Console\Commands')
    ->classes()
    ->toHaveMethod('handle');

arch('laravel-commands-wrong-path')
    ->expect('App')
    ->not->toExtend('Illuminate\Console\Command')
    ->ignoring('App\Console\Commands');

arch('laravel-mail')
    ->expect('App\Mail')
    ->classes()
    ->toExtend('Illuminate\Mail\Mailable');

arch('laravel-mail-wrong-path')
    ->expect('App')
    ->not->toExtend('Illuminate\Mail\Mailable')
    ->ignoring('App\Mail');

arch('laravel-jobs-handle')
    ->expect('App\Jobs')
    ->classes()
    ->toHaveMethod('handle');

arch('laravel-listeners')
    ->expect('App\Listeners')
    ->toHaveMethod('handle');

arch('laravel-providers-extends')
    ->expect('App\Providers')
    ->toExtend('Illuminate\Support\ServiceProvider');

arch('laravel-service-provider-wrong-path')
    ->expect('App')
    ->not->toExtend('Illuminate\Support\ServiceProvider')
    ->ignoring('App\Providers');

arch('laravel-notification-mail-queue')
    ->expect('App\Notifications\Mail')
    ->classes()
    ->toImplement('Illuminate\Contracts\Queue\ShouldQueue');
