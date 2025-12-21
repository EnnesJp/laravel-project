<?php

declare(strict_types=1);

namespace App\Rules;

use App\ValueObjects\Document\Factory\DocumentFactory;
use Illuminate\Contracts\Validation\Rule;

class DocumentRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        return DocumentFactory::isValidDocument($value);
    }

    public function message(): string
    {
        return 'The :attribute must be a valid CPF or CNPJ.';
    }
}
