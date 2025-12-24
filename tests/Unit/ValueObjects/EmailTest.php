<?php

declare(strict_types=1);

use App\ValueObjects\Email;

it('creates a valid email', function () {
    $email = new Email('user@example.com');

    expect($email->getValue())->toBe('user@example.com');
    expect((string) $email)->toBe('user@example.com');
});

it('normalizes email case', function () {
    $email = new Email('USER@EXAMPLE.COM');

    expect($email->getValue())->toBe('user@example.com');
});

it('trims whitespace', function () {
    $email = new Email('  user@example.com  ');

    expect($email->getValue())->toBe('user@example.com');
});

it('can get local part', function () {
    $email = new Email('user@example.com');

    expect($email->getLocalPart())->toBe('user');
});

it('can get domain', function () {
    $email = new Email('user@example.com');

    expect($email->getDomain())->toBe('example.com');
});

it('can create from string', function () {
    $email = Email::fromString('user@example.com');

    expect($email->getValue())->toBe('user@example.com');
});

it('compares emails correctly', function () {
    $email1 = new Email('user@example.com');
    $email2 = new Email('user@example.com');
    $email3 = new Email('other@example.com');

    expect($email1->equals($email2))->toBeTrue();
    expect($email1->equals($email3))->toBeFalse();
});

it('serializes to json', function () {
    $email = new Email('user@example.com');

    expect(json_encode($email))->toBe('"user@example.com"');
});

it('throws exception for empty email', function () {
    expect(fn () => new Email(''))->toThrow(InvalidArgumentException::class);
});

it('throws exception for email without @ symbol', function () {
    expect(fn () => new Email('userexample.com'))->toThrow(InvalidArgumentException::class);
});

it('throws exception for email with multiple @ symbols', function () {
    expect(fn () => new Email('user@@example.com'))->toThrow(InvalidArgumentException::class);
});

it('throws exception for email without domain', function () {
    expect(fn () => new Email('user@'))->toThrow(InvalidArgumentException::class);
});

it('throws exception for email without local part', function () {
    expect(fn () => new Email('@example.com'))->toThrow(InvalidArgumentException::class);
});

it('throws exception for email with consecutive dots', function () {
    expect(fn () => new Email('user..name@example.com'))->toThrow(InvalidArgumentException::class);
});

it('throws exception for local part too long', function () {
    $longLocalPart = str_repeat('a', 65) . '@example.com';
    expect(fn () => new Email($longLocalPart))->toThrow(InvalidArgumentException::class);
});

it('throws exception for domain too long', function () {
    $longDomain = 'user@' . str_repeat('a', 254) . '.com';
    expect(fn () => new Email($longDomain))->toThrow(InvalidArgumentException::class);
});
