<?php

declare(strict_types=1);

use App\ValueObjects\Document\Cpf;

it('creates a valid CPF', function () {
    $cpf = new Cpf('12345678909');

    expect($cpf->getValue())->toBe('12345678909');
    expect($cpf->getFormatted())->toBe('123.456.789-09');
});

it('creates CPF from formatted string', function () {
    $cpf = new Cpf('123.456.789-09');

    expect($cpf->getValue())->toBe('12345678909');
    expect($cpf->getFormatted())->toBe('123.456.789-09');
});

it('throws exception for invalid CPF', function () {
    expect(fn () => new Cpf('12345678901'))->toThrow(InvalidArgumentException::class);
});

it('throws exception for CPF with all same digits', function () {
    expect(fn () => new Cpf('11111111111'))->toThrow(InvalidArgumentException::class);
});

it('throws exception for CPF with wrong length', function () {
    expect(fn () => new Cpf('123456789'))->toThrow(InvalidArgumentException::class);
});

it('generates valid CPF', function () {
    $cpf = Cpf::generate();

    expect($cpf)->toBeInstanceOf(Cpf::class);
    expect(strlen($cpf->getValue()))->toBe(11);
});

it('compares CPFs correctly', function () {
    $cpf1 = new Cpf('12345678909');
    $cpf2 = new Cpf('123.456.789-09');
    $cpf3 = new Cpf('98765432100');

    expect($cpf1->equals($cpf2))->toBeTrue();
    expect($cpf1->equals($cpf3))->toBeFalse();
});

it('converts to string', function () {
    $cpf = new Cpf('12345678909');

    expect((string) $cpf)->toBe('123.456.789-09');
});

it('serializes to JSON', function () {
    $cpf = new Cpf('12345678909');

    expect(json_encode($cpf))->toBe('"123.456.789-09"');
});
