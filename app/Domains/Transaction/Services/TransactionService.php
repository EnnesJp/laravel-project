<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\DTOs\DepositDTO;
use App\Domains\Transaction\DTOs\TransferDTO;
use App\Domains\Transaction\Events\TransactionFailed;
use App\Domains\Transaction\Exceptions\InvalidDepositException;
use App\Domains\Transaction\Exceptions\InvalidTransferException;
use App\Domains\Transaction\Models\Transaction;

class TransactionService
{
    public function __construct(
        private readonly DepositService $depositService,
        private readonly TransferService $transferService
    ) {
    }

    /**
     * @throws InvalidDepositException
     */
    public function deposit(DepositDTO $dto): Transaction
    {
        try {
            return $this->depositService->deposit($dto);
        } catch (\Exception $e) {
            event(new TransactionFailed($dto->payee, $dto->payer));
            throw $e;
        }
    }

    /**
     * @throws InvalidTransferException
     */
    public function transfer(TransferDTO $dto, int $currentUserId): Transaction
    {
        try {
            return $this->transferService->transfer($dto, $currentUserId);
        } catch (\Exception $e) {
            event(new TransactionFailed($dto->payee, $dto->payer));
            throw $e;
        }
    }
}
