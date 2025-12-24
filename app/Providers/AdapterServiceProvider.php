<?php

declare(strict_types=1);

namespace App\Providers;

use App\Adapters\Contracts\NotificationAdapterInterface;
use App\Adapters\Factories\NotificationAdapterFactory;
use App\Domains\Transaction\Adapters\Contracts\ValidationAdapterInterface;
use App\Domains\Transaction\Adapters\Factories\ValidationAdapterFactory;
use Illuminate\Support\ServiceProvider;

class AdapterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerNotificationAdapter();
        $this->registerValidationAdapter();
    }

    private function registerNotificationAdapter(): void
    {
        $this->app->singleton(NotificationAdapterInterface::class, function () {
            $type   = config('adapters.notification.type', 'http');
            $config = config('adapters.notification.config', []);

            return NotificationAdapterFactory::create($type, $config);
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
