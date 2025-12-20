<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\DepositDTO;
use App\DTOs\Transaction\TransferDTO;
use App\Exceptions\InvalidDepositException;
use App\Exceptions\InvalidTransferException;

class TransactionValidationService
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * @throws InvalidTransferException
     */
    public function validateTransfer(TransferDTO $dto): void
    {
        if ($dto->amount <= 0) {
            throw InvalidTransferException::invalidAmount($dto->amount);
        }

        if ($dto->payer === $dto->payee) {
            throw InvalidTransferException::sameUser();
        }

        $payer = $this->userService->findById($dto->payer);
        $payee = $this->userService->findById($dto->payee);

        if (!$payer) {
            throw InvalidTransferException::userNotFound($dto->payer);
        }

        if (!$payee) {
            throw InvalidTransferException::userNotFound($dto->payee);
        }

        if (!$payer->canTransfer()) {
            throw InvalidTransferException::invalidPayerRole($payer->role);
        }

        if (!$payee->canReciveTransfer()) {
            throw InvalidTransferException::invalidPayeeRole($payee->role);
        }
    }

    /**
     * @throws InvalidDepositException
     */
    public function validateDeposit(DepositDTO $dto): void
    {
        if ($dto->amount <= 0) {
            throw InvalidDepositException::invalidAmount($dto->amount);
        }

        if ($dto->payer === $dto->payee) {
            throw InvalidDepositException::sameUser();
        }

        $payer = $this->userService->findById($dto->payer);
        $payee = $this->userService->findById($dto->payee);

        if (!$payer) {
            throw InvalidDepositException::userNotFound($dto->payer);
        }

        if (!$payee) {
            throw InvalidDepositException::userNotFound($dto->payee);
        }

        if (!$payer->canDeposit()) {
            throw InvalidDepositException::invalidPayerRole($payer->role);
        }

        if (!$payee->canReciveDeposit()) {
            throw InvalidDepositException::invalidPayeeRole($payee->role);
        }
    }
}
