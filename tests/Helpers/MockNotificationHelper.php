<?php

declare(strict_types=1);

namespace Tests\Helpers;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\Adapters\Mocks\MockNotificationAdapter;

class MockNotificationHelper
{
    public static function bindMock(bool $shouldLog = true): void
    {
        app()->bind(NotificationAdapterInterface::class, function () use ($shouldLog) {
            return new MockNotificationAdapter($shouldLog);
        });
    }
}
