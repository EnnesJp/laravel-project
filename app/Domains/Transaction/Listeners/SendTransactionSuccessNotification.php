<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Listeners;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\Domains\Transaction\DTOs\TransactionSuccessNotificationDTO;
use App\Domains\Transaction\Events\TransactionSuccess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTransactionSuccessNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries         = 3;
    public int $maxExceptions = 3;
    public array $backoff     = [1, 5, 10];

    public function __construct(
        private readonly NotificationAdapterInterface $notificationAdapter
    ) {
    }

    public function handle(TransactionSuccess $event): void
    {
        $notification = TransactionSuccessNotificationDTO::fromEvent($event);

        try {
            $this->notificationAdapter->send($notification);
        } catch (\Exception $e) {
            Log::warning('Failed to send transaction success notification', [
                'attempt'        => $this->attempts(),
                'max_attempts'   => $this->tries,
                'transaction_id' => $event->transactionId ?? 'unknown',
                'error'          => $e->getMessage(),
            ]);

            if ($this->attempts() >= $this->tries) {
                Log::error('Transaction success notification failed after all retry attempts', [
                    'transaction_id' => $event->transactionId ?? 'unknown',
                    'total_attempts' => $this->attempts(),
                    'final_error'    => $e->getMessage(),
                ]);
            }

            throw $e;
        }
    }

    public function retryUntil(): \DateTime
    {
        return now()->addMinutes(10);
    }
}
