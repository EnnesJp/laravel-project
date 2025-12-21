<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domains\Transaction\Adapters\Contracts\ValidationAdapterInterface;
use App\Domains\Transaction\Adapters\HttpValidationAdapter;
use Illuminate\Support\ServiceProvider;

class ValidationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ValidationAdapterInterface::class, function () {
            return new HttpValidationAdapter(
                baseUrl: config('validation.service.url'),
                timeoutSeconds: config('validation.service.timeout', 10)
            );
        });
    }
}
