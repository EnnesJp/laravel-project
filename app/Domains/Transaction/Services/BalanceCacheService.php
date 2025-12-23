<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\Repositories\Contracts\RemainingCreditRepositoryInterface;
use App\Repositories\Contracts\CacheRepositoryInterface;

class BalanceCacheService
{
    private const CACHE_PREFIX = 'user_balance:';
    private const CACHE_TTL    = 3600;

    public function __construct(
        private readonly RemainingCreditRepositoryInterface $repository,
        private readonly CacheRepositoryInterface $cacheRepository
    ) {
    }

    public function getUserBalance(int $userId): int
    {
        $cacheKey = $this->getCacheKey($userId);

        return (int) $this->cacheRepository->remember($cacheKey, self::CACHE_TTL, function () use ($userId) {
            $availableCredits = $this->repository->getRemainingCreditsByUserId($userId);
            return $availableCredits->sum('remaining');
        });
    }

    public function updateUserBalance(int $userId, int $balanceChange): void
    {
        $cacheKey = $this->getCacheKey($userId);

        $currentBalance = $this->getUserBalance($userId);
        $newBalance     = $currentBalance + $balanceChange;

        $this->cacheRepository->put($cacheKey, $newBalance, self::CACHE_TTL);
    }

    public function invalidateUserBalance(int $userId): void
    {
        $cacheKey = $this->getCacheKey($userId);
        $this->cacheRepository->forget($cacheKey);
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
