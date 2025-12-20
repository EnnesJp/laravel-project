<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\DepositDTO;
use App\DTOs\Transaction\TransferDTO;
use App\Exceptions\InvalidDepositException;
use App\Exceptions\InvalidTransferException;
use App\Models\Transaction;

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
        return $this->depositService->deposit($dto);
    }

    /**
     * @throws InvalidTransferException
     */
    public function transfer(TransferDTO $dto, int $currentUserId): Transaction
    {
        return $this->transferService->transfer($dto, $currentUserId);
    }
}
