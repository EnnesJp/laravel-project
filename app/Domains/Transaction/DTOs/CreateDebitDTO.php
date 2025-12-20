<?php

declare(strict_types=1);

namespace App\Domains\Transaction\DTOs;

class CreateDebitDTO
{
    public function __construct(
        public readonly int $transactionId,
        public readonly int $creditId,
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
            'credit_id'      => $this->creditId,
            'amount'         => $this->amount,
        ];
    }
}
