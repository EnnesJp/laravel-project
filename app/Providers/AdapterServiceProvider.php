<?php

declare(strict_types=1);

namespace App\Providers;

use App\Adapters\Contracts\NotificationManagerInterface;
use App\Adapters\Factories\NotificationManagerFactory;
use App\Domains\Transaction\Adapters\Contracts\ValidationAdapterInterface;
use App\Domains\Transaction\Adapters\Factories\ValidationAdapterFactory;
use Illuminate\Support\ServiceProvider;

class AdapterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerNotificationManager();
        $this->registerValidationAdapter();
    }

    private function registerNotificationManager(): void
    {
        $this->app->singleton(NotificationManagerInterface::class, function () {
            $channelsConfig = config('adapters.notification.channels', []);

            $activeChannels = array_filter($channelsConfig, function ($config) {
                return !empty($config['config']['url'] ?? '');
            });

            return NotificationManagerFactory::create($activeChannels);
        });
    }

    private function registerValidationAdapter(): void
    {
        $this->app->singleton(ValidationAdapterInterface::class, function () {
            $type   = config('adapters.validation.type', 'http');
            $config = config('adapters.validation.config', []);

            return ValidationAdapterFactory::create($type, $config);
        });
    }
}
