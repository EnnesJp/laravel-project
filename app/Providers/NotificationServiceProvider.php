<?php

declare(strict_types=1);

namespace App\Providers;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\Adapters\HttpNotificationAdapter;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(NotificationAdapterInterface::class, function () {
            return new HttpNotificationAdapter(
                baseUrl: config('notification.service.url'),
                timeoutSeconds: (int) config('notification.service.timeout', 10)
            );
        });
    }
}
