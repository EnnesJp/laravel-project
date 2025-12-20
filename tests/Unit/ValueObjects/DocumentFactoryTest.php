<?php

declare(strict_types=1);

use App\ValueObjects\Document\Cnpj;
use App\ValueObjects\Document\Cpf;
use App\ValueObjects\Document\Factory\DocumentFactory;

it('creates CPF from factory', function () {
    $document = DocumentFactory::create('12345678909');

    expect($document)->toBeInstanceOf(Cpf::class);
    expect($document->getValue())->toBe('12345678909');
});

it('creates CNPJ from factory', function () {
    $document = DocumentFactory::create('11222333000181');

    expect($document)->toBeInstanceOf(Cnpj::class);
    expect($document->getValue())->toBe('11222333000181');
});

it('creates CPF with formatting', function () {
    $document = DocumentFactory::create('123.456.789-09');

    expect($document)->toBeInstanceOf(Cpf::class);
    expect($document->getValue())->toBe('12345678909');
});

it('creates CNPJ with formatting', function () {
    $document = DocumentFactory::create('11.222.333/0001-81');

    expect($document)->toBeInstanceOf(Cnpj::class);
    expect($document->getValue())->toBe('11222333000181');
});

it('throws exception for invalid length', function () {
    expect(fn () => DocumentFactory::create('123456789'))->toThrow(InvalidArgumentException::class);
});

it('creates specific CPF', function () {
    $cpf = DocumentFactory::createCpf('12345678909');

    expect($cpf)->toBeInstanceOf(Cpf::class);
});

it('creates specific CNPJ', function () {
    $cnpj = DocumentFactory::createCnpj('11222333000181');

    expect($cnpj)->toBeInstanceOf(Cnpj::class);
});

it('validates document correctly', function () {
    expect(DocumentFactory::isValidDocument('12345678909'))->toBeTrue();
    expect(DocumentFactory::isValidDocument('11222333000181'))->toBeTrue();
    expect(DocumentFactory::isValidDocument('12345678901'))->toBeFalse();
    expect(DocumentFactory::isValidDocument('123456789'))->toBeFalse();
});

it('gets document type correctly', function () {
    expect(DocumentFactory::getDocumentType('12345678909'))->toBe('CPF');
    expect(DocumentFactory::getDocumentType('11222333000181'))->toBe('CNPJ');
    expect(DocumentFactory::getDocumentType('123456789'))->toBeNull();
});
