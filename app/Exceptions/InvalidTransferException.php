<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InvalidTransferException extends InvalidActionBaseException
{
    public function __construct(string $message = 'Invalid transfer operation', int $code = 422, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function insufficientBalance(int $available, int $required): self
    {
        return new self("Insufficient balance. Available: {$available}, Required: {$required}");
    }

    public static function invalidPayerRole(string $userRole): self
    {
        return new self("User with role '{$userRole}' cannot perform transfers");
    }

    public static function invalidPayeeRole(string $userRole): self
    {
        return new self("User with role '{$userRole}' cannot recive transfers");
    }
}
