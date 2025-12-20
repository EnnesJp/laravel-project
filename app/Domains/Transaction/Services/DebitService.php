<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\DTOs\CreateDebitDTO;
use App\Domains\Transaction\DTOs\CreateFundDebitDTO;
use App\Domains\Transaction\Models\Debit;
use App\Domains\Transaction\Models\FundDebit;
use App\Domains\Transaction\Repositories\Contracts\DebitRepositoryInterface;
use App\Domains\Transaction\Repositories\Contracts\FundDebitRepositoryInterface;
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
