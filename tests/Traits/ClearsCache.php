<?php

declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsCache
{
    protected function clearRedisCache(): void
    {
        Cache::store('redis')->flush();
    }
}
