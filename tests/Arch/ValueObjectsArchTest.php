<?php

arch('value-objects-are-immutable')
    ->expect('App\ValueObjects')
    ->classes()
    ->toBeReadonly()
    ->ignoring([
        'App\ValueObjects\Document\Factory',
    ]);

arch('value-objects-implement-stringable')
    ->expect('App\ValueObjects')
    ->classes()
    ->toImplement(Stringable::class)
    ->ignoring([
        'App\ValueObjects\Document\Factory',
    ]);

arch('value-objects-implement-json-serializable')
    ->expect('App\ValueObjects')
    ->classes()
    ->toImplement(JsonSerializable::class)
    ->ignoring([
        'App\ValueObjects\Document\Factory',
    ]);

arch('value-objects-have-equals-method')
    ->expect('App\ValueObjects')
    ->classes()
    ->toHaveMethod('equals')
    ->ignoring([
        'App\ValueObjects\Document\Factory',
    ]);

arch('value-objects-have-get-value-method')
    ->expect('App\ValueObjects')
    ->classes()
    ->toHaveMethod('getValue')
    ->ignoring([
        'App\ValueObjects\Document\Factory',
    ]);

arch('document-value-objects-extend-document')
    ->expect('App\ValueObjects')
    ->classes()
    ->toExtend('App\ValueObjects\Document')
    ->ignoring([
        'App\ValueObjects\Document',
        'App\ValueObjects\Document\Factory',
    ]);

arch('value-objects-not-use-eloquent')
    ->expect('App\ValueObjects')
    ->not->toUse('Illuminate\Database\Eloquent');

arch('value-objects-not-use-http')
    ->expect('App\ValueObjects')
    ->not->toUse('Illuminate\Http');

arch('factory-class-naming')
    ->expect('App\ValueObjects\Document\Factory')
    ->toHaveSuffix('Factory');
