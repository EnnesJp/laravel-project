<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Adapters\Mocks;

use App\Domains\Transaction\Adapters\Contracts\ValidationAdapterInterface;
use App\Domains\Transaction\DTOs\TransferDTO;
use App\Domains\Transaction\Exceptions\ExternalValidationException;

class MockValidationAdapter implements ValidationAdapterInterface
{
    public function __construct(
        private readonly bool $shouldPass = true,
        private readonly string $failureReason = 'Mock validation failed'
    ) {
    }

    /**
     * @throws ExternalValidationException
     */
    public function validateTransfer(TransferDTO $dto): bool
    {
        if (!$this->shouldPass) {
            throw ExternalValidationException::validationFailed($this->failureReason);
        }

        return true;
    }
}
