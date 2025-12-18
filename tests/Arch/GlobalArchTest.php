<?php

arch('strict-types')
    ->expect('App')
    ->toUseStrictTypes();

arch('laravel-not-to-use-functions')
    ->expect([
        'dd',
        'ddd',
        'dump',
        'env',
        'exit',
        'ray',
    ])->not->toBeUsed();
