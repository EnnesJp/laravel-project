<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Exceptions;

use App\Domains\Transaction\Exceptions\Base\InvalidActionBaseException;

class InvalidTransferException extends InvalidActionBaseException
{
    public static function insufficientBalance(int $available, int $required): static
    {
        $formattedAvailable = number_format($available / 100, 2, ',', '.');
        $formattedRequired  = number_format($required / 100, 2, ',', '.');

        return new static("Insufficient balance. Available: {$formattedAvailable}, Required: {$formattedRequired}");
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
