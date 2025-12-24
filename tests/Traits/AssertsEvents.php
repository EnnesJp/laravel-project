<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Domains\Transaction\Events\TransactionFailed;
use App\Domains\Transaction\Events\TransactionSuccess;
use Illuminate\Support\Facades\Event;

trait AssertsEvents
{
    protected function assertTransactionSuccessEvent(int $payeeUserId, ?int $payerUserId = null): void
    {
        Event::assertDispatched(TransactionSuccess::class, function ($event) use ($payeeUserId, $payerUserId) {
            $payeeMatches = $event->payeeUserId === $payeeUserId;
            $payerMatches = $payerUserId        === null || $event->payerUserId === $payerUserId;

            return $payeeMatches && $payerMatches;
        });
    }

    protected function assertTransactionFailedEvent(int $payeeUserId, ?int $payerUserId = null): void
    {
        Event::assertDispatched(TransactionFailed::class, function ($event) use ($payeeUserId, $payerUserId) {
            $payeeMatches = $event->payeeUserId === $payeeUserId;
            $payerMatches = $payerUserId        === null || $event->payerUserId === $payerUserId;

            return $payeeMatches && $payerMatches;
        });
    }
}
