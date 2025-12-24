<?php

declare(strict_types=1);

namespace App\Adapters\Factories;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\Adapters\GmailNotificationAdapter;
use App\Adapters\Mocks\MockNotificationAdapter;
use App\Adapters\TwilioNotificationAdapter;
use InvalidArgumentException;

class NotificationAdapterFactory
{
    public static function create(string $type, array $config = []): NotificationAdapterInterface
    {
        return match ($type) {
            'gmail' => new GmailNotificationAdapter(
                baseUrl: $config['url']    ?? '',
                apiKey: $config['api_key'] ?? '',
                timeoutSeconds: (int) ($config['timeout'] ?? 10)
            ),
            'twilio' => new TwilioNotificationAdapter(
                baseUrl: $config['url']    ?? '',
                apiKey: $config['api_key'] ?? '',
                timeoutSeconds: (int) ($config['timeout'] ?? 10)
            ),
            'mock'  => new MockNotificationAdapter(),
            default => throw new InvalidArgumentException("Unknown notification adapter type: {$type}")
        };
    }
}
