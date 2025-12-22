<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Listeners;

use App\Domains\Transaction\Events\TransactionFailed;
use App\Domains\Transaction\Services\BalanceCacheService;

class InvalidateBalanceCache
{
    public function __construct(
        private readonly BalanceCacheService $cacheService
    ) {
    }

    public function handle(TransactionFailed $event): void
    {
        $this->cacheService->invalidateUserBalance($event->payerUserId);

        if ($event->payeeUserId) {
            $this->cacheService->invalidateUserBalance($event->payeeUserId);
        }
    }
}
