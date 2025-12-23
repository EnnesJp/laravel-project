<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Listeners;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\Domains\Transaction\DTOs\TransactionSuccessNotificationDTO;
use App\Domains\Transaction\Events\TransactionSuccess;

class SendTransactionSuccessNotification
{
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
