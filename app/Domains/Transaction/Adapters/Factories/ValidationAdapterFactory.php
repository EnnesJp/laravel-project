<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Adapters\Factories;

use App\Domains\Transaction\Adapters\Contracts\ValidationAdapterInterface;
use App\Domains\Transaction\Adapters\HttpValidationAdapter;
use App\Domains\Transaction\Adapters\Mocks\MockValidationAdapter;
use InvalidArgumentException;

class ValidationAdapterFactory
{
    /**
     * @param array<string, mixed> $config
     */
    public static function create(string $type, array $config = []): ValidationAdapterInterface
    {
        return match ($type) {
            'http' => new HttpValidationAdapter(
                baseUrl: $config['url'] ?? '',
                timeoutSeconds: (int) ($config['timeout'] ?? 10)
            ),
            'mock' => new MockValidationAdapter(
                shouldPass: (bool) ($config['should_pass'] ?? true),
            ),
            default => throw new InvalidArgumentException("Unknown validation adapter type: {$type}")
        };
    }
}
