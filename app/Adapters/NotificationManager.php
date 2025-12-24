<?php

declare(strict_types=1);

namespace App\Adapters;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\Adapters\Contracts\NotificationManagerInterface;
use App\DTOs\NotificationDTO;
use InvalidArgumentException;

class NotificationManager implements NotificationManagerInterface
{
    /**
     * @param array<string, NotificationAdapterInterface> $adapters
     */
    public function __construct(
        private readonly array $adapters
    ) {
    }

    public function sendEmail(NotificationDTO $notification): void
    {
        $this->sendToChannel($notification, 'email');
    }

    public function sendSms(NotificationDTO $notification): void
    {
        $this->sendToChannel($notification, 'sms');
    }

    private function sendToChannel(NotificationDTO $notification, string $channel): void
    {
        if (!isset($this->adapters[$channel])) {
            throw new InvalidArgumentException("Unknown notification channel: {$channel}");
        }

        $this->adapters[$channel]->send($notification);
    }
}
