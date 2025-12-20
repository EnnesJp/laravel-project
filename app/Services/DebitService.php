<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\CreateDebitDTO;
use App\DTOs\Transaction\CreateFundDebitDTO;
use App\Models\Debit;
use App\Models\FundDebit;
use App\Repositories\Contracts\DebitRepositoryInterface;
use App\Repositories\Contracts\FundDebitRepositoryInterface;
use Illuminate\Support\Collection;

class DebitService
{
    public function __construct(
        private readonly DebitRepositoryInterface $debitRepository,
        private readonly FundDebitRepositoryInterface $fundDebitRepository
    ) {
    }

    public function createFundDebit(int $transactionId, int $amount): FundDebit
    {
        $debitDTO = new CreateFundDebitDTO(
            transactionId: $transactionId,
            amount: $amount
        );

        return $this->fundDebitRepository->create($debitDTO);
    }

    public function createDebit(int $transactionId, int $creditId, int $amount): Debit
    {
        $debitDTO = new CreateDebitDTO(
            transactionId: $transactionId,
            creditId: $creditId,
            amount: $amount
        );

        return $this->debitRepository->create($debitDTO);
    }

    /**
     * @param Collection<int, CreateDebitDTO> $debitsToCreate
     */
    public function bulkCreateDebits(Collection $debitsToCreate): void
    {
        $this->debitRepository->bulkInsert($debitsToCreate);
    }
}
