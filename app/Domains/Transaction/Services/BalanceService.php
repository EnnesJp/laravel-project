<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\DTOs\CreateDebitDTO;
use App\Domains\Transaction\Exceptions\InvalidTransferException;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\Transaction\Repositories\Contracts\RemainingCreditRepositoryInterface;
use Illuminate\Support\Collection;

class BalanceService
{
    public function __construct(
        private readonly RemainingCreditRepositoryInterface $repository,
        private readonly BalanceCacheService $cacheService
    ) {
    }

    /**
     * @throws InvalidTransferException
     * @return Collection<int, CreateDebitDTO>
     */
    public function calculateDebits(int $userId, int $amount, Transaction $transaction): Collection
    {
        $availableBalance = $this->cacheService->getUserBalance($userId);

        if ($availableBalance < $amount) {
            throw InvalidTransferException::insufficientBalance($availableBalance, $amount);
        }

        $availableCredits = $this->repository->getRemainingCreditsByUserId($userId);

        $debitsToCreate  = collect();
        $remainingAmount = $amount;

        foreach ($availableCredits as $credit) {
            if ($remainingAmount <= 0) {
                break;
            }

            $debitAmount = min($remainingAmount, $credit->remaining);

            $debitDTO = new CreateDebitDTO(
                transactionId: $transaction->id,
                creditId: $credit->credit_id,
                amount: $debitAmount
            );

            $debitsToCreate->push($debitDTO);
            $remainingAmount -= $debitAmount;
        }

        return $debitsToCreate;
    }
}
