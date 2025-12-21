<?php

declare(strict_types=1);

namespace App\ValueObjects\Document\Factory;

use App\ValueObjects\Document\Base\Document;
use App\ValueObjects\Document\Cnpj;
use App\ValueObjects\Document\Cpf;
use InvalidArgumentException;

class DocumentFactory
{
    public static function create(string $value): Document
    {
        $cleanValue = preg_replace('/[^0-9]/', '', $value);

        return match (strlen($cleanValue)) {
            11      => new Cpf($value),
            14      => new Cnpj($value),
            default => throw new InvalidArgumentException("Invalid document length: {$value}")
        };
    }

    public static function createCpf(string $value): Cpf
    {
        return new Cpf($value);
    }

    public static function createCnpj(string $value): Cnpj
    {
        return new Cnpj($value);
    }

    public static function isValidDocument(string $value): bool
    {
        try {
            self::create($value);
            return true;
        } catch (InvalidArgumentException) {
            return false;
        }
    }

    public static function getDocumentType(string $value): ?string
    {
        $cleanValue = preg_replace('/[^0-9]/', '', $value);

        return match (strlen($cleanValue)) {
            11      => 'CPF',
            14      => 'CNPJ',
            default => null
        };
    }
}
