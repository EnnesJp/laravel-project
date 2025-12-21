<?php

declare(strict_types=1);

namespace App\ValueObjects\Document;

use App\ValueObjects\Document\Base\Document;

readonly class Cnpj extends Document
{
    protected function isValid(string $value): bool
    {
        if (strlen($value) !== 14) {
            return false;
        }

        if (preg_match('/(\d)\1{13}/', $value)) {
            return false;
        }

        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum     = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $value[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $digit1    = $remainder < 2 ? 0 : 11 - $remainder;

        if ($value[12] != $digit1) {
            return false;
        }

        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum     = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $value[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $digit2    = $remainder < 2 ? 0 : 11 - $remainder;

        return $value[13] == $digit2;
    }

    protected function format(string $value): string
    {
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $value);
    }

    protected function getType(): string
    {
        return 'CNPJ';
    }

    public static function generate(): self
    {
        $cnpj = '';

        for ($i = 0; $i < 8; $i++) {
            $cnpj .= rand(0, 9);
        }
        $cnpj .= '0001';

        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum     = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $digit1    = $remainder < 2 ? 0 : 11 - $remainder;
        $cnpj .= $digit1;

        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum     = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $digit2    = $remainder < 2 ? 0 : 11 - $remainder;
        $cnpj .= $digit2;

        return new self($cnpj);
    }
}
