<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Listeners;

use App\Domains\Transaction\Events\TransactionSuccess;
use App\Domains\Transaction\Services\BalanceCacheService;

class RefreshBalanceCache
{
    public function __construct(
        private readonly BalanceCacheService $cacheService
    ) {
    }

    public function handle(TransactionSuccess $event): void
    {
        $this->cacheService->refreshUserBalance($event->payerUserId);

        if ($event->payeeUserId) {
            $this->cacheService->refreshUserBalance($event->payeeUserId);
        }
    }
}
