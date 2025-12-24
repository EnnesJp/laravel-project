<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use Illuminate\Support\Facades\Cache;

class UserLockingService
{
    private const LOCK_TIMEOUT     = 'app.redis_lock_timeout';
    private const LOCK_MAX_RETRIES = 'app.redis_lock_max_retries';

    /**
     * @param array<int> $userIds
     */
    public function lockUsersForOperation(array $userIds, callable $callback): mixed
    {
        sort($userIds);

        return $this->acquireLocksRecursively($userIds, 0, $callback);
    }

    /**
     * @param array<int> $userIds
     */
    private function acquireLocksRecursively(array $userIds, int $index, callable $callback): mixed
    {
        if ($index >= count($userIds)) {
            return $callback();
        }

        $userId = $userIds[$index];
        $lock   = Cache::lock("user:{$userId}", config(self::LOCK_TIMEOUT));

        return $lock->block(config(self::LOCK_MAX_RETRIES), function () use ($userIds, $index, $callback) {
            return $this->acquireLocksRecursively($userIds, $index + 1, $callback);
        });
    }
}
