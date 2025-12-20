<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InvalidDepositException extends InvalidActionBaseException
{
    public function __construct(string $message = 'Invalid deposit operation', int $code = 422, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function invalidPayerRole(string $userRole): self
    {
        return new self("Payer with role '{$userRole}' cannot be used for deposits. Only external_found users can be payers");
    }

    public static function invalidPayeeRole(string $userRole): self
    {
        return new self("Payee with role '{$userRole}' cannot recive deposits.");
    }
}
