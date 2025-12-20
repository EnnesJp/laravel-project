<?php

declare(strict_types=1);

namespace App\DTOs\Transaction;

use App\Http\Requests\DepositRequest;

class DepositDTO
{
    public function __construct(
        public readonly int $amount,
        public readonly int $payee,
        public readonly int $payer,
    ) {
    }

    public static function fromRequest(DepositRequest $request): self
    {
        return new self(
            amount: $request->validated('value'),
            payee: $request->validated('payee'),
            payer: $request->validated('payer'),
        );
    }
}
