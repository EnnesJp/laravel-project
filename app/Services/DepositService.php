<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\CreateTransactionDTO;
use App\DTOs\Transaction\DepositDTO;
use App\Enums\TransactionType;
use App\Exceptions\InvalidDepositException;
use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DepositService
{
    public function __construct(
        private readonly TransactionValidationService $validationService,
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
            $transactionDTO = new CreateTransactionDTO(
                payerUserId: $dto->payer,
                payeeUserId: $dto->payee,
                type: TransactionType::DEPOSIT
            );
            $transaction = $this->repository->create($transactionDTO);

            $this->creditService->createCredit($transaction->id, $dto->amount);
            $this->debitService->createFundDebit($transaction->id, $dto->amount);

            return $this->repository->findByIdWithRelations(
                $transaction->id,
                ['credits', 'debits']
            );
        });
    }
}
