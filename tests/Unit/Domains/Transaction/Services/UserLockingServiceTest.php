<?php

declare(strict_types=1);

use App\Domains\Transaction\Services\UserLockingService;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->service = new UserLockingService();
    Cache::spy();
});

describe('lockUsersForOperation', function () {
    it('sorts user IDs before locking to prevent deadlocks', function () {
        $userIds  = [2, 1];
        $callback = fn () => 'executed';

        $mockLock = Mockery::mock(Lock::class);
        $mockLock->shouldReceive('block')
            ->with(config('app.redis_lock_max_retries'), Mockery::type('callable'))
            ->times(2)
            ->andReturnUsing(function ($retries, $callback) {
                return $callback();
            });

        Cache::shouldReceive('lock')
            ->with('user:1', config('app.redis_lock_timeout'))
            ->once()
            ->andReturn($mockLock);

        Cache::shouldReceive('lock')
            ->with('user:2', config('app.redis_lock_timeout'))
            ->once()
            ->andReturn($mockLock);

        $result = $this->service->lockUsersForOperation($userIds, $callback);

        expect($result)->toBe('executed');
    });

    it('locks multiple users in sorted order', function () {
        $userIds        = [5, 2];
        $executionOrder = [];

        $callback = function () use (&$executionOrder) {
            $executionOrder[] = 'callback_executed';
            return 'multiple users success';
        };

        $mockLock = Mockery::mock(Lock::class);
        $mockLock->shouldReceive('block')
            ->with(config('app.redis_lock_max_retries'), Mockery::type('callable'))
            ->times(2)
            ->andReturnUsing(function ($retries, $callback) {
                return $callback();
            });

        Cache::shouldReceive('lock')
            ->with('user:2', config('app.redis_lock_timeout'))
            ->once()
            ->andReturn($mockLock);

        Cache::shouldReceive('lock')
            ->with('user:5', config('app.redis_lock_timeout'))
            ->once()
            ->andReturn($mockLock);

        $result = $this->service->lockUsersForOperation($userIds, $callback);

        expect($result)->toBe('multiple users success');
        expect($executionOrder)->toBe(['callback_executed']);
    });
});
