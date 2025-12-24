<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Listeners;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\Domains\Transaction\DTOs\TransactionSuccessNotificationDTO;
use App\Domains\Transaction\Events\TransactionSuccess;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendTransactionSuccessNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private readonly NotificationAdapterInterface $notificationAdapter
    ) {
    }

    public function handle(TransactionSuccess $event): void
    {
        $notification = TransactionSuccessNotificationDTO::fromEvent($event);
        $this->notificationAdapter->send($notification);
    }
}
