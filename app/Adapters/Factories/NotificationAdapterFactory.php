<?php

declare(strict_types=1);

namespace App\Adapters\Factories;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\Adapters\HttpNotificationAdapter;
use App\Adapters\Mocks\MockNotificationAdapter;
use InvalidArgumentException;

class NotificationAdapterFactory
{
    public static function create(string $type, array $config = []): NotificationAdapterInterface
    {
        return match ($type) {
            'http' => new HttpNotificationAdapter(
                baseUrl: $config['url'] ?? '',
                timeoutSeconds: (int) ($config['timeout'] ?? 10)
            ),
            'mock'  => new MockNotificationAdapter(),
            default => throw new InvalidArgumentException("Unknown notification adapter type: {$type}")
        };
    }
}
