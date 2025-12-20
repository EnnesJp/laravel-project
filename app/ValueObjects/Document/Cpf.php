<?php

declare(strict_types=1);

namespace App\ValueObjects\Document;

use App\ValueObjects\Document\Base\Document;

readonly class Cpf extends Document
{
    protected function isValid(string $value): bool
    {
        if (strlen($value) !== 11) {
            return false;
        }

        if (preg_match('/(\d)\1{10}/', $value)) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $value[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1    = $remainder < 2 ? 0 : 11 - $remainder;

        if ($value[9] != $digit1) {
            return false;
        }

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $value[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2    = $remainder < 2 ? 0 : 11 - $remainder;

        return $value[10] == $digit2;
    }

    protected function format(string $value): string
    {
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $value);
    }

    protected function getType(): string
    {
        return 'CPF';
    }

    public static function generate(): self
    {
        $cpf = '';

        for ($i = 0; $i < 9; $i++) {
            $cpf .= rand(0, 9);
        }

        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $digit1    = $remainder < 2 ? 0 : 11 - $remainder;
        $cpf .= $digit1;

        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $digit2    = $remainder < 2 ? 0 : 11 - $remainder;
        $cpf .= $digit2;

        return new self($cpf);
    }
}
