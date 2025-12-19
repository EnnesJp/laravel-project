<?php

declare(strict_types=1);

namespace App\DTOs\Transaction;

class CreateFundDebitDTO
{
    public function __construct(
        public readonly int $entry_id,
        public readonly int $amount,
    ) {
    }

    public function toArray(): array
    {
        return [
            'entry_id' => $this->entry_id,
            'amount'   => $this->amount,
        ];
    }
}
