<?php

declare(strict_types=1);

namespace App\DTOs\Transaction;

class CreateDebitDTO
{
    public function __construct(
        public readonly int $transaction_id,
        public readonly int $credit_id,
        public readonly int $amount,
    ) {
    }

    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transaction_id,
            'credit_id'      => $this->credit_id,
            'amount'         => $this->amount,
        ];
    }
}
