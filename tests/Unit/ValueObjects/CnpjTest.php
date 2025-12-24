<?php

declare(strict_types=1);

use App\ValueObjects\Document\Cnpj;

it('creates a valid CNPJ', function () {
    $cnpj = new Cnpj('11222333000181');

    expect($cnpj->getValue())->toBe('11222333000181');
    expect($cnpj->getFormatted())->toBe('11.222.333/0001-81');
});

it('creates CNPJ from formatted string', function () {
    $cnpj = new Cnpj('11.222.333/0001-81');

    expect($cnpj->getValue())->toBe('11222333000181');
    expect($cnpj->getFormatted())->toBe('11.222.333/0001-81');
});

it('throws exception for invalid CNPJ', function () {
    expect(fn () => new Cnpj('11222333000180'))->toThrow(InvalidArgumentException::class);
});

it('throws exception for CNPJ with all same digits', function () {
    expect(fn () => new Cnpj('11111111111111'))->toThrow(InvalidArgumentException::class);
});

it('throws exception for CNPJ with wrong length', function () {
    expect(fn () => new Cnpj('1122233300018'))->toThrow(InvalidArgumentException::class);
});

it('generates valid CNPJ', function () {
    $cnpj = Cnpj::generate();

    expect($cnpj)->toBeInstanceOf(Cnpj::class);
    expect(strlen($cnpj->getValue()))->toBe(14);
});

it('compares CNPJs correctly', function () {
    $cnpj1 = new Cnpj('11222333000181');
    $cnpj2 = new Cnpj('11.222.333/0001-81');
    $cnpj3 = new Cnpj('14352627000116');

    expect($cnpj1->equals($cnpj2))->toBeTrue();
    expect($cnpj1->equals($cnpj3))->toBeFalse();
});

it('converts to string', function () {
    $cnpj = new Cnpj('11222333000181');

    expect((string) $cnpj)->toBe('11.222.333/0001-81');
});
