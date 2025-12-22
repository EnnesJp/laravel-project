<?php

declare(strict_types=1);

namespace Tests\Helpers;

use App\Domains\Transaction\Adapters\Contracts\ValidationAdapterInterface;
use App\Domains\Transaction\Adapters\Mocks\MockValidationAdapter;

class MockValidationHelper
{
    public static function bindSuccessfulMock(): void
    {
        app()->bind(ValidationAdapterInterface::class, function () {
            return new MockValidationAdapter(true);
        });
    }

    public static function bindFailingMock(string $reason = 'Mock validation failed'): void
    {
        app()->bind(ValidationAdapterInterface::class, function () use ($reason) {
            return new MockValidationAdapter(false, $reason);
        });
    }
}
