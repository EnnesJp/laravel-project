<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\CreateCreditDTO;
use App\DTOs\Transaction\CreateFundDebitDTO;
use App\DTOs\Transaction\CreateTransactionDTO;
use App\DTOs\Transaction\DepositDTO;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Repositories\Contracts\CreditRepositoryInterface;
use App\Repositories\Contracts\FundDebitRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly CreditRepositoryInterface $creditRepository,
        private readonly FundDebitRepositoryInterface $debitRepository
    ) {
    }

    public function deposit(DepositDTO $dto): Transaction
    {
        return DB::transaction(function () use ($dto) {
            $transactionDTO = new CreateTransactionDTO(
                payer_user_id: $dto->payer,
                payee_user_id: $dto->payee,
                type: TransactionType::DEPOSIT
            );
            $transaction = $this->transactionRepository->create($transactionDTO);

            $creditDTO = new CreateCreditDTO(
                entry_id: $transaction->id,
                amount: $dto->amount
            );
            $this->creditRepository->create($creditDTO);

            $debitDTO = new CreateFundDebitDTO(
                entry_id: $transaction->id,
                amount: $dto->amount
            );
            $this->debitRepository->create($debitDTO);

            return $this->transactionRepository->findByIdWithRelations(
                $transaction->id,
                ['credits', 'debits']
            );
        });
    }
}
