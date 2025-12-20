<?php

declare(strict_types=1);

namespace App\DTOs\Transaction;

class CreateFundDebitDTO
{
    public function __construct(
        public readonly int $transactionId,
        public readonly int $amount,
    ) {
    }

    /**
     * @return array<string, int>
     */
    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'amount'         => $this->amount,
        ];
    }
}
