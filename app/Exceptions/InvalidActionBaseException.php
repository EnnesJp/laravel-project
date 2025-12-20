<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InvalidActionBaseException extends Exception
{
    public function __construct(string $message, int $code = 422, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function sameUser(): self
    {
        return new self('Cannot create deposit where payer and payee are the same user');
    }

    public static function invalidAmount(int $amount): self
    {
        return new self("Invalid deposit amount: {$amount}. Amount must be greater than 0");
    }

    public static function userNotFound(int $userId): self
    {
        return new self("User with ID {$userId} not found");
    }
}
