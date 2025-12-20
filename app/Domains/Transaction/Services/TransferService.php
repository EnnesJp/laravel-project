<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\DTOs\CreateTransactionDTO;
use App\Domains\Transaction\DTOs\TransferDTO;
use App\Domains\Transaction\Enums\TransactionType;
use App\Domains\Transaction\Exceptions\InvalidTransferException;
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
        private readonly BalanceService $balanceService
    ) {
    }

    /**
     * @throws InvalidTransferException
     */
    public function transfer(TransferDTO $dto, int $currentUserId): Transaction
    {
        $this->validationService->validateTransfer($dto, $currentUserId);

        return DB::transaction(function () use ($dto) {
            $transactionDTO = new CreateTransactionDTO(
                payerUserId: $dto->payer,
                payeeUserId: $dto->payee,
                type: TransactionType::TRANSFER
            );
            $transaction = $this->repository->create($transactionDTO);

            $this->creditService->createCredit($transaction->id, $dto->amount);

            $debitsToCreate = $this->balanceService->calculateDebitsFromBalance(
                $dto->payer,
                $dto->amount,
                $transaction
            );

            $this->debitService->bulkCreateDebits($debitsToCreate);

            return $this->repository->findByIdWithRelations(
                $transaction->id,
                ['credits', 'debits']
            );
        });
    }
}
