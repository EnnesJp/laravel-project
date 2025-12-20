<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\CreateCreditDTO;
use App\Models\Credit;
use App\Repositories\Contracts\CreditRepositoryInterface;

class CreditService
{
    public function __construct(
        private readonly CreditRepositoryInterface $creditRepository
    ) {
    }

    public function createCredit(int $transactionId, int $amount): Credit
    {
        $creditDTO = new CreateCreditDTO(
            transactionId: $transactionId,
            amount: $amount
        );

        return $this->creditRepository->create($creditDTO);
    }
}
