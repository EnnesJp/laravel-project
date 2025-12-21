<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\DTOs\CreateCreditDTO;
use App\Domains\Transaction\Models\Credit;
use App\Domains\Transaction\Repositories\Contracts\CreditRepositoryInterface;

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
