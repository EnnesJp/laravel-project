<?php

declare(strict_types=1);

namespace App\Domains\Transaction\DTOs;

use App\Http\Requests\TransferRequest;

class TransferDTO
{
    public function __construct(
        public readonly int $amount,
        public readonly int $payee,
        public readonly int $payer,
    ) {
    }

    public static function fromRequest(TransferRequest $request): self
    {
        return new self(
            amount: $request->validated('value'),
            payee: $request->validated('payee'),
            payer: $request->validated('payer'),
        );
    }
}
