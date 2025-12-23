<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionFailed
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public readonly int $payeeUserId,
        public readonly ?int $payerUserId = null
    ) {
    }
}
