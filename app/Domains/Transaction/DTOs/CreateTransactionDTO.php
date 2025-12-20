<?php

declare(strict_types=1);

namespace App\Domains\Transaction\DTOs;

use App\Domains\Transaction\Enums\TransactionType;

class CreateTransactionDTO
{
    public function __construct(
        public readonly int $payerUserId,
        public readonly int $payeeUserId,
        public readonly TransactionType $type,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'payer_user_id' => $this->payerUserId,
            'payee_user_id' => $this->payeeUserId,
            'type'          => $this->type,
        ];
    }
}
