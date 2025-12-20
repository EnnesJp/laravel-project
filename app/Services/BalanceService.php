<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\CreateDebitDTO;
use App\Exceptions\InvalidTransferException;
use App\Models\Transaction;
use App\Repositories\Contracts\RemainingCreditRepositoryInterface;
use Illuminate\Support\Collection;

class BalanceService
{
    public function __construct(
        private readonly RemainingCreditRepositoryInterface $repository
    ) {
    }

    /**
     * @throws InvalidTransferException
     * @return Collection<int, CreateDebitDTO>
     */
    public function calculateDebitsFromBalance(int $userId, int $amount, Transaction $transaction): Collection
    {
        $availableCredits = $this->repository->getRemainingCreditsByUserId($userId);
        $availableBalance = $availableCredits->sum('remaining');

        if ($availableBalance < $amount) {
            throw InvalidTransferException::insufficientBalance($availableBalance, $amount);
        }

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
