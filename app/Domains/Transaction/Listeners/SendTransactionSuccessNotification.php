<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Listeners;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\Domains\Transaction\Events\TransactionSuccess;

class SendTransactionSuccessNotification
{
    public function __construct(
        private readonly NotificationAdapterInterface $notificationAdapter
    ) {
    }

    public function handle(TransactionSuccess $event): void
    {
        $payload = [
            'type'          => 'transaction_success',
            'payee_user_id' => $event->payeeUserId,
            'payer_user_id' => $event->payerUserId,
            'timestamp'     => now()->toISOString(),
        ];

        $this->notificationAdapter->send($payload);
    }
}
