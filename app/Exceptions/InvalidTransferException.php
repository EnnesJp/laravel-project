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

    public static function insufficientBalance(int $available, int $required): static
    {
        return new static("Insufficient balance. Available: {$available}, Required: {$required}");
    }

    public static function invalidPayerRole(string $userRole): static
    {
        return new static("User with role '{$userRole}' cannot perform transfers");
    }

    public static function invalidPayeeRole(string $userRole): static
    {
        return new static("User with role '{$userRole}' cannot recive transfers");
    }
}
