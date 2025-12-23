<?php

declare(strict_types=1);

namespace App\Adapters\Mocks;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\DTOs\NotificationDTO;
use Illuminate\Support\Facades\Log;

class MockNotificationAdapter implements NotificationAdapterInterface
{
    public function __construct(
        private readonly bool $shouldLog = true
    ) {
    }

    public function send(NotificationDTO $notification): void
    {
        if ($this->shouldLog) {
            Log::info('Mock notification sent', [
                'type'    => $notification->type,
                'payload' => $notification->toArray(),
            ]);
        }
    }
}
