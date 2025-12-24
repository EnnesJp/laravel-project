<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\Adapters\Contracts\ValidationAdapterInterface;
use App\Domains\Transaction\DTOs\CreateTransactionDTO;
use App\Domains\Transaction\DTOs\TransferDTO;
use App\Domains\Transaction\Enums\TransactionType;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\Transaction\Repositories\Contracts\TransactionRepositoryInterface;
use App\Domains\Transaction\Services\Validation\TransferValidationService;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function __construct(
        private readonly TransferValidationService $validationService,
        private readonly TransactionRepositoryInterface $repository,
        private readonly CreditService $creditService,
        private readonly DebitService $debitService,
        private readonly ValidationAdapterInterface $externalValidation,
        private readonly UserLockingService $lockingService
    ) {
    }

    public function transfer(TransferDTO $dto, int $currentUserId): Transaction
    {
        $this->validationService->validateTransferData($dto, $currentUserId);

        return $this->lockingService->lockUsersForOperation(
            [$dto->payer, $dto->payee],
            fn () => $this->executeTransferTransaction($dto)
        );
    }

    private function executeTransferTransaction(TransferDTO $dto): Transaction
    {
        return DB::transaction(function () use ($dto) {
            $transaction = $this->createTransferTransaction($dto, TransactionType::TRANSFER);
            $this->processTransferEntries($transaction, $dto);
            $this->externalValidation->validateTransfer($dto);

            return $this->repository->findByIdWithRelations(
                $transaction->id,
                ['credit']
            );
        });
    }

    private function createTransferTransaction(TransferDTO $dto, TransactionType $type): Transaction
    {
        $transactionDTO = new CreateTransactionDTO(
            payerUserId: $dto->payer,
            payeeUserId: $dto->payee,
            type: $type
        );

        return $this->repository->create($transactionDTO);
    }

    private function processTransferEntries(Transaction $transaction, TransferDTO $dto): void
    {
        $this->creditService->createCredit($transaction->id, $dto->amount);
        $this->debitService->createDebits($dto->payer, $dto->amount, $transaction->id);
    }
}
