<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Repositories;

use App\Domains\Transaction\Repositories\Contracts\CacheRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class RedisCacheRepository implements CacheRepositoryInterface
{
    private const CACHE_STORE = 'redis';

    public function __construct(
        private readonly string $cacheStore = self::CACHE_STORE
    ) {
    }

    public function put(string $key, mixed $value, int $ttl): bool
    {
        return Cache::store($this->cacheStore)->put($key, $value, $ttl);
    }

    public function remember(string $key, int $ttl, callable $callback): mixed
    {
        return Cache::store($this->cacheStore)->remember($key, $ttl, $callback);
    }

    public function forget(string $key): bool
    {
        return Cache::store($this->cacheStore)->forget($key);
    }
}
