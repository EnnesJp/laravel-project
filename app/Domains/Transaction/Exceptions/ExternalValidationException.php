<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Exceptions;

use Exception;

class ExternalValidationException extends Exception
{
    public static function serviceUnavailable(): self
    {
        return new self('External validation service is unavailable', 503);
    }

    public static function validationFailed(string $reason): self
    {
        return new self("External validation failed: {$reason}", 422);
    }

    public static function invalidResponse(): self
    {
        return new self('Invalid response from external validation service', 502);
    }
}
