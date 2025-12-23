<?php

declare(strict_types=1);

namespace App\DTOs;

abstract class NotificationDTO
{
    public function __construct(
        public readonly string $type,
        public readonly string $timestamp
    ) {
    }

    abstract public function toArray(): array;

    protected static function now(): string
    {
        return now()->toISOString();
    }
}
