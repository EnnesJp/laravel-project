<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

interface CacheRepositoryInterface
{
    public function put(string $key, mixed $value, int $ttl): bool;

    public function remember(string $key, int $ttl, callable $callback): mixed;

    public function forget(string $key): bool;
}
