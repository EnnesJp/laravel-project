<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Exceptions;

class InvalidDepositException extends InvalidActionBaseException
{
    public static function invalidPayerRole(string $userRole): static
    {
        return new static("Payer with role '{$userRole}' cannot be used for deposits. Only external_found users can be payers");
    }

    public static function invalidPayeeRole(string $userRole): static
    {
        return new static("Payee with role '{$userRole}' cannot recive deposits.");
    }
}
