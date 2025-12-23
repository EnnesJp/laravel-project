<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Services;

use App\Domains\Transaction\Exceptions\InvalidTransferException;
use App\Domains\Transaction\Models\RemainingCredit;
use App\Domains\Transaction\Repositories\Contracts\RemainingCreditRepositoryInterface;
use Illuminate\Support\Collection;

class BalanceService
{
    public function __construct(
        private readonly RemainingCreditRepositoryInterface $repository,
        private readonly BalanceCacheService $cacheService
    ) {
    }

    /**
     * @return Collection<int, RemainingCredit>
     */
    public function getRemainingCredits(int $userId): Collection
    {
        return $this->repository->getRemainingCreditsByUserId($userId);
    }

    /**
     * @throws InvalidTransferException
     */
    public function validateUserBalance(int $userId, int $amount): void
    {
        $availableBalance = $this->cacheService->getUserBalance($userId);

        if ($availableBalance < $amount) {
            throw InvalidTransferException::insufficientBalance($availableBalance, $amount);
        }
    }
}
