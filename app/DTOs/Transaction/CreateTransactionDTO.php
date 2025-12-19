<?php

declare(strict_types=1);

namespace App\DTOs\Transaction;

use App\Enums\TransactionType;

class CreateTransactionDTO
{
    public function __construct(
        public readonly int $payer_user_id,
        public readonly int $payee_user_id,
        public readonly TransactionType $type,
    ) {
    }

    public function toArray(): array
    {
        return [
            'payer_user_id' => $this->payer_user_id,
            'payee_user_id' => $this->payee_user_id,
            'type'          => $this->type,
        ];
    }
}
