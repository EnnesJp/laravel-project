<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class InvalidActionBaseException extends Exception
{
    final public function __construct(string $message = 'Invalid operation', int $code = 422, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function sameUser(): static
    {
        return new static('Cannot create deposit where payer and payee are the same user');
    }

    public static function invalidAmount(int $amount): static
    {
        return new static("Invalid deposit amount: {$amount}. Amount must be greater than 0");
    }

    public static function userNotFound(int $userId): static
    {
        return new static("User with ID {$userId} not found");
    }
}
