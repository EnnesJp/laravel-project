<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\Transaction\DepositDTO;
use App\Enums\TransactionType;
use App\Models\Credit;
use App\Models\Debit;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function deposit(DepositDTO $dto): Transaction
    {
        return DB::transaction(function () use ($dto) {
            $transaction = Transaction::create([
                'payer_user_id' => $dto->payer,
                'payee_user_id' => $dto->payee,
                'type'          => TransactionType::DEPOSIT,
            ]);

            $credit = Credit::create([
                'entry_id' => $transaction->id,
                'amount'   => $dto->amount,
            ]);

            Debit::create([
                'entry_id'  => $transaction->id,
                'credit_id' => $credit->id,
                'amount'    => $dto->amount,
            ]);

            return $transaction->load(['credits', 'debits']);
        });
    }
}
