<?php

declare(strict_types=1);

namespace App\Exceptions;

class InvalidTransferException extends InvalidActionBaseException
{
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

    public static function userCannotTransferForOthers(): static
    {
        return new static("Users with 'user' role can only transfer their own money");
    }
}
