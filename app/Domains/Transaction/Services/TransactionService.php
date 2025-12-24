<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\DTOs\DepositDTO;
use App\Domains\Transaction\DTOs\TransferDTO;
use App\Domains\Transaction\Events\TransactionFailed;
use App\Domains\Transaction\Events\TransactionSuccess;
use App\Domains\Transaction\Models\Transaction;

class TransactionService
{
    public function __construct(
        private readonly DepositService $depositService,
        private readonly TransferService $transferService
    ) {
    }

    public function deposit(DepositDTO $dto): Transaction
    {
        try {
            $transaction = $this->depositService->deposit($dto);
            event(new TransactionSuccess($transaction->id, $dto->payee, $dto->payer));
            return $transaction;
        } catch (\Exception $e) {
            event(new TransactionFailed($dto->payee, $dto->payer));
            throw $e;
        }
    }

    public function transfer(TransferDTO $dto, int $currentUserId): Transaction
    {
        try {
            $transaction = $this->transferService->transfer($dto, $currentUserId);
            event(new TransactionSuccess($transaction->id, $dto->payee, $dto->payer));
            return $transaction;
        } catch (\Exception $e) {
            event(new TransactionFailed($dto->payee, $dto->payer));
            throw $e;
        }
    }
}
