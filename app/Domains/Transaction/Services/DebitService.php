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
        private readonly FundDebitRepositoryInterface $fundDebitRepository,
        private readonly BalanceService $balanceService
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

    public function createDebits(int $userId, int $amount, int $transactionId): void
    {
        $debitsToCreate = $this->calculateDebits(
            $userId,
            $amount,
            $transactionId
        );

        $this->bulkCreateDebits($debitsToCreate);
    }

    /**
     * @return Collection<int, CreateDebitDTO>
     */
    public function calculateDebits(int $userId, int $amount, int $transactionId): Collection
    {
        $this->balanceService->validateUserBalance($userId, $amount);
        $availableCredits = $this->balanceService->getRemainingCredits($userId);

        $debitsToCreate  = collect();
        $remainingAmount = $amount;

        foreach ($availableCredits as $credit) {
            if ($remainingAmount <= 0) {
                break;
            }

            $debitAmount = min($remainingAmount, $credit->remaining);

            $debitDTO = new CreateDebitDTO(
                transactionId: $transactionId,
                creditId: $credit->credit_id,
                amount: $debitAmount
            );

            $debitsToCreate->push($debitDTO);
            $remainingAmount -= $debitAmount;
        }

        return $debitsToCreate;
    }

    /**
     * @param Collection<int, CreateDebitDTO> $debitsToCreate
     */
    public function bulkCreateDebits(Collection $debitsToCreate): void
    {
        $this->debitRepository->bulkInsert($debitsToCreate);
    }
}
