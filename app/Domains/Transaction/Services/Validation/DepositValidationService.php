<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services\Validation;

use App\Domains\Transaction\DTOs\DepositDTO;
use App\Domains\Transaction\Exceptions\InvalidDepositException;
use App\Domains\User\Models\User;
use App\Domains\User\Services\UserService;

class DepositValidationService
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * @throws InvalidDepositException
     */
    public function validateDeposit(DepositDTO $dto): void
    {
        $this->validateDepositAmount($dto->amount);
        $this->validateDepositParticipants($dto->payer, $dto->payee);
        $this->validateDepositUsers($dto->payer, $dto->payee);
    }

    /**
     * @throws InvalidDepositException
     */
    private function validateDepositAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw InvalidDepositException::invalidAmount($amount);
        }
    }

    /**
     * @throws InvalidDepositException
     */
    private function validateDepositParticipants(int $payerId, int $payeeId): void
    {
        if ($payerId === $payeeId) {
            throw InvalidDepositException::sameUser();
        }
    }

    /**
     * @throws InvalidDepositException
     */
    private function validateDepositUsers(int $payerId, int $payeeId): void
    {
        $payer = $this->userService->findById($payerId);
        $payee = $this->userService->findById($payeeId);

        $this->validateDepositPayerExists($payer, $payerId);
        $this->validateDepositPayeeExists($payee, $payeeId);
        $this->validatePayerCanDeposit($payer);
        $this->validatePayeeCanReceiveDeposit($payee);
    }

    /**
     * @throws InvalidDepositException
     */
    private function validateDepositPayerExists(?User $payer, int $payerId): void
    {
        if (!$payer) {
            throw InvalidDepositException::userNotFound($payerId);
        }
    }

    /**
     * @throws InvalidDepositException
     */
    private function validateDepositPayeeExists(?User $payee, int $payeeId): void
    {
        if (!$payee) {
            throw InvalidDepositException::userNotFound($payeeId);
        }
    }

    /**
     * @throws InvalidDepositException
     */
    private function validatePayerCanDeposit(User $payer): void
    {
        if (!$payer->canDeposit()) {
            throw InvalidDepositException::invalidPayerRole($payer->role);
        }
    }

    /**
     * @throws InvalidDepositException
     */
    private function validatePayeeCanReceiveDeposit(User $payee): void
    {
        if (!$payee->canReciveDeposit()) {
            throw InvalidDepositException::invalidPayeeRole($payee->role);
        }
    }
}
