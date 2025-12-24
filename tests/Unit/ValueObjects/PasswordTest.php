<?php

declare(strict_types=1);

use App\ValueObjects\Password;

it('creates a valid password', function () {
    $password = Password::fromString('MyP@ssw0rd');

    expect($password)->toBeInstanceOf(Password::class);
    expect($password->getValue())->toBe('MyP@ssw0rd');
});

it('can get hashed password', function () {
    $password = Password::fromString('MyP@ssw0rd');
    $hashed   = $password->getHashed();

    expect($hashed)->not->toBe('MyP@ssw0rd');
    expect(password_verify('MyP@ssw0rd', $hashed))->toBeTrue();
});

it('compares passwords correctly', function () {
    $password1 = Password::fromString('MyP@ssw0rd');
    $password2 = Password::fromString('MyP@ssw0rd');
    $password3 = Password::fromString('Different123!');

    expect($password1->equals($password2))->toBeTrue();
    expect($password1->equals($password3))->toBeFalse();
});

it('converts to string', function () {
    $password = Password::fromString('MyP@ssw0rd');

    expect((string) $password)->toBe('MyP@ssw0rd');
});

it('hides password value in json serialization', function () {
    $password = Password::fromString('MyP@ssw0rd');

    expect($password->jsonSerialize())->toBe('***');
});

it('throws exception for password too short', function () {
    expect(fn () => Password::fromString('short'))->toThrow(
        InvalidArgumentException::class,
        'Password must be at least 8 characters long and contain letters, numbers, and symbols.'
    );
});

it('throws exception for password without letters', function () {
    expect(fn () => Password::fromString('12345678!'))->toThrow(
        InvalidArgumentException::class,
        'Password must be at least 8 characters long and contain letters, numbers, and symbols.'
    );
});

it('throws exception for password without numbers', function () {
    expect(fn () => Password::fromString('Password!'))->toThrow(
        InvalidArgumentException::class,
        'Password must be at least 8 characters long and contain letters, numbers, and symbols.'
    );
});

it('throws exception for password without symbols', function () {
    expect(fn () => Password::fromString('Password123'))->toThrow(
        InvalidArgumentException::class,
        'Password must be at least 8 characters long and contain letters, numbers, and symbols.'
    );
});
