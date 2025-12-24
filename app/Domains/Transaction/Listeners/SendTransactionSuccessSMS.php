<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Listeners;

use App\Adapters\Contracts\NotificationManagerInterface;
use App\Domains\Transaction\DTOs\TransactionSuccessNotificationDTO;
use App\Domains\Transaction\Events\TransactionSuccess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendTransactionSuccessSMS implements ShouldQueue
{
    use InteractsWithQueue;

    public int $maxExceptions = 3;
    /**
     * @var array<int, int>
     */
    public array $backoff = [1, 5, 10];

    public function __construct(
        private readonly NotificationManagerInterface $notificationManager
    ) {
    }

    public function handle(TransactionSuccess $event): void
    {
        $notification = TransactionSuccessNotificationDTO::fromEvent($event);

        try {
            $this->notificationManager->sendSms($notification);
        } catch (\Exception $e) {
            Log::warning('Failed to send sms notification', [
                'attempt'        => $this->attempts(),
                'max_attempts'   => $this->maxExceptions,
                'transaction_id' => $event->transactionId ?? 'unknown',
                'error'          => $e->getMessage(),
            ]);

            if ($this->attempts() >= $this->maxExceptions) {
                Log::error('Transaction success sms notification failed after all retry attempts', [
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
