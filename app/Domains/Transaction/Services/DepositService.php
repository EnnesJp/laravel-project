<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\DTOs\CreateTransactionDTO;
use App\Domains\Transaction\DTOs\DepositDTO;
use App\Domains\Transaction\Enums\TransactionType;
use App\Domains\Transaction\Exceptions\InvalidDepositException;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\Transaction\Repositories\Contracts\TransactionRepositoryInterface;
use App\Domains\Transaction\Services\Validation\DepositValidationService;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public function __construct(
        private readonly DepositValidationService $validationService,
        private readonly TransactionRepositoryInterface $repository,
        private readonly CreditService $creditService,
        private readonly DebitService $debitService
    ) {
    }

    /**
     * @throws InvalidDepositException
     */
    public function deposit(DepositDTO $dto): Transaction
    {
        $this->validationService->validateDeposit($dto);

        return DB::transaction(function () use ($dto) {
            $transaction = $this->createTransferTransaction($dto, TransactionType::DEPOSIT);
            $this->processTransferEntries($transaction, $dto);

            return $this->repository->findByIdWithRelations(
                $transaction->id,
                ['credit']
            );
        });
    }

    private function createTransferTransaction(DepositDTO $dto, TransactionType $type): Transaction
    {
        $transactionDTO = new CreateTransactionDTO(
            payerUserId: $dto->payer,
            payeeUserId: $dto->payee,
            type: $type
        );

        return $this->repository->create($transactionDTO);
    }

    private function processTransferEntries(Transaction $transaction, DepositDTO $dto): void
    {
        $this->creditService->createCredit($transaction->id, $dto->amount);
        $this->debitService->createFundDebit($transaction->id, $dto->amount);
    }
}
