<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services\Validation;

use App\Domains\Transaction\DTOs\TransferDTO;
use App\Domains\Transaction\Exceptions\InvalidTransferException;
use App\Domains\User\Enums\UserRole;
use App\Domains\User\Models\User;
use App\Domains\User\Services\UserService;

class TransferValidationService
{
    public function __construct(
        private readonly UserService $userService
    ) {
    }

    /**
     * @throws InvalidTransferException
     */
    public function validateTransferData(TransferDTO $dto, int $currentUserId): void
    {
        $this->validateTransferAmount($dto->amount);
        $this->validateTransferParticipants($dto->payer, $dto->payee);
        $this->validateCurrentUserPermissions($currentUserId, $dto->payer);
        $this->validateTransferUsers($dto->payer, $dto->payee);
    }

    /**
     * @throws InvalidTransferException
     */
    private function validateTransferAmount(int $amount): void
    {
        if ($amount <= 0) {
            throw InvalidTransferException::invalidAmount($amount);
        }
    }

    /**
     * @throws InvalidTransferException
     */
    private function validateTransferParticipants(int $payerId, int $payeeId): void
    {
        if ($payerId === $payeeId) {
            throw InvalidTransferException::sameUser();
        }
    }

    /**
     * @throws InvalidTransferException
     */
    private function validateCurrentUserPermissions(int $currentUserId, int $payerId): void
    {
        $currentUser = $this->userService->findById($currentUserId);

        if ($currentUser && $currentUser->role === UserRole::USER->value && $currentUser->id !== $payerId) {
            throw InvalidTransferException::userCannotTransferForOthers();
        }
    }

    /**
     * @throws InvalidTransferException
     */
    private function validateTransferUsers(int $payerId, int $payeeId): void
    {
        $payer = $this->userService->findById($payerId);
        $payee = $this->userService->findById($payeeId);

        $this->validatePayerExists($payer, $payerId);
        $this->validatePayeeExists($payee, $payeeId);
        $this->validatePayerCanTransfer($payer);
        $this->validatePayeeCanReceiveTransfer($payee);
    }

    /**
     * @throws InvalidTransferException
     */
    private function validatePayerExists(?User $payer, int $payerId): void
    {
        if (!$payer) {
            throw InvalidTransferException::userNotFound($payerId);
        }
    }

    /**
     * @throws InvalidTransferException
     */
    private function validatePayeeExists(?User $payee, int $payeeId): void
    {
        if (!$payee) {
            throw InvalidTransferException::userNotFound($payeeId);
        }
    }

    /**
     * @throws InvalidTransferException
     */
    private function validatePayerCanTransfer(User $payer): void
    {
        if (!$payer->canTransfer()) {
            throw InvalidTransferException::invalidPayerRole($payer->role);
        }
    }

    /**
     * @throws InvalidTransferException
     */
    private function validatePayeeCanReceiveTransfer(User $payee): void
    {
        if (!$payee->canReciveTransfer()) {
            throw InvalidTransferException::invalidPayeeRole($payee->role);
        }
    }
}
