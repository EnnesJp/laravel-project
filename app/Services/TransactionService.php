<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\DepositDTO;
use App\Enums\TransactionType;
use App\Models\Transaction;
use App\Repositories\Contracts\CreditRepositoryInterface;
use App\Repositories\Contracts\DebitRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private readonly TransactionRepositoryInterface $transactionRepository,
        private readonly CreditRepositoryInterface $creditRepository,
        private readonly DebitRepositoryInterface $debitRepository
    ) {
    }

    public function deposit(DepositDTO $dto): Transaction
    {
        return DB::transaction(function () use ($dto) {
            $transaction = $this->transactionRepository->create([
                'payer_user_id' => $dto->payer,
                'payee_user_id' => $dto->payee,
                'type'          => TransactionType::DEPOSIT,
            ]);

            $credit = $this->creditRepository->create([
                'entry_id' => $transaction->id,
                'amount'   => $dto->amount,
            ]);

            $this->debitRepository->create([
                'entry_id'  => $transaction->id,
                'credit_id' => $credit->id,
                'amount'    => $dto->amount,
            ]);

            return $this->transactionRepository->findByIdWithRelations(
                $transaction->id,
                ['payer', 'payee', 'credits', 'debits']
            );
        });
    }
}
