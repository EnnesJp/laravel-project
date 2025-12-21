<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Adapters\Contracts;

use App\Domains\Transaction\DTOs\TransferDTO;

interface ValidationAdapterInterface
{
    /**
     * @throws \App\Domains\Transaction\Exceptions\ExternalValidationException
     */
    public function validateTransfer(TransferDTO $dto): bool;
}
