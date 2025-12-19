<?php

declare(strict_types=1);

namespace App\DTOs\Transaction;

class CreateDebitDTO
{
    public function __construct(
        public readonly int $entry_id,
        public readonly int $credit_id,
        public readonly int $amount,
    ) {
    }

    public function toArray(): array
    {
        return [
            'entry_id'  => $this->entry_id,
            'credit_id' => $this->credit_id,
            'amount'    => $this->amount,
        ];
    }
}
