<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\Repositories\Contracts\RemainingCreditRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class BalanceCacheService
{
    private const CACHE_PREFIX = 'user_balance:';
    private const CACHE_TTL    = 3600; // 1 hour
    private const CACHE_STORE  = 'redis'; // Use Redis specifically

    public function __construct(
        private readonly RemainingCreditRepositoryInterface $repository
    ) {
    }

    public function getUserBalance(int $userId): int
    {
        $cacheKey = $this->getCacheKey($userId);

        return Cache::store(self::CACHE_STORE)->remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $availableCredits = $this->repository->getRemainingCreditsByUserId($userId);
            return $availableCredits->sum('remaining');
        });
    }

    public function updateUserBalance(int $userId, int $balanceChange): void
    {
        $cacheKey = $this->getCacheKey($userId);

        $currentBalance = $this->getUserBalance($userId);
        $newBalance     = $currentBalance + $balanceChange;

        Cache::store(self::CACHE_STORE)->put($cacheKey, $newBalance, self::CACHE_TTL);
    }

    public function invalidateUserBalance(int $userId): void
    {
        $cacheKey = $this->getCacheKey($userId);
        Cache::store(self::CACHE_STORE)->forget($cacheKey);
    }

    public function refreshUserBalance(int $userId): int
    {
        $this->invalidateUserBalance($userId);
        return $this->getUserBalance($userId);
    }

    private function getCacheKey(int $userId): string
    {
        return self::CACHE_PREFIX . $userId;
    }
}
