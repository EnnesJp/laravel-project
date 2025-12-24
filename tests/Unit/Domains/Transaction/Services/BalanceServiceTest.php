<?php

declare(strict_types=1);

use App\Domains\Transaction\Exceptions\InvalidTransferException;
use App\Domains\Transaction\Repositories\Contracts\RemainingCreditRepositoryInterface;
use App\Domains\Transaction\Services\BalanceCacheService;
use App\Domains\Transaction\Services\BalanceService;

beforeEach(function () {
    $this->repository   = Mockery::mock(RemainingCreditRepositoryInterface::class);
    $this->cacheService = Mockery::mock(BalanceCacheService::class);
    $this->service      = new BalanceService($this->repository, $this->cacheService);
});

describe('validateUserBalance', function () {
    it('throws exception when user has insufficient balance', function () {
        $userId           = 1;
        $amount           = 10000;
        $availableBalance = 5000;

        $this->cacheService->shouldReceive('getUserBalance')
            ->with($userId)
            ->andReturn($availableBalance);

        expect(fn () => $this->service->validateUserBalance($userId, $amount))
            ->toThrow(InvalidTransferException::class, 'Insufficient balance. Available: 50,00, Required: 100,00');
    });

    it('throws exception when user has zero balance', function () {
        $userId           = 1;
        $amount           = 1000;
        $availableBalance = 0;

        $this->cacheService->shouldReceive('getUserBalance')
            ->with($userId)
            ->andReturn($availableBalance);

        expect(fn () => $this->service->validateUserBalance($userId, $amount))
            ->toThrow(InvalidTransferException::class, 'Insufficient balance. Available: 0,00, Required: 10,00');
    });

    it('throws exception with correct formatting for large amounts', function () {
        $userId           = 1;
        $amount           = 123456789;
        $availableBalance = 98765432;

        $this->cacheService->shouldReceive('getUserBalance')
            ->with($userId)
            ->andReturn($availableBalance);

        expect(fn () => $this->service->validateUserBalance($userId, $amount))
            ->toThrow(InvalidTransferException::class, 'Insufficient balance. Available: 987.654,32, Required: 1.234.567,89');
    });

    it('handles small cent amounts correctly', function () {
        $userId           = 1;
        $amount           = 99;
        $availableBalance = 50;

        $this->cacheService->shouldReceive('getUserBalance')
            ->with($userId)
            ->andReturn($availableBalance);

        expect(fn () => $this->service->validateUserBalance($userId, $amount))
            ->toThrow(InvalidTransferException::class, 'Insufficient balance. Available: 0,50, Required: 0,99');
    });


    it('handles different user IDs correctly', function () {
        $userId1 = 123;
        $userId2 = 456;
        $amount  = 5000;

        $this->cacheService->shouldReceive('getUserBalance')
            ->with($userId1)
            ->andReturn(10000);

        $this->cacheService->shouldReceive('getUserBalance')
            ->with($userId2)
            ->andReturn(2000);

        $this->service->validateUserBalance($userId1, $amount);
        expect(true)->toBeTrue();

        expect(fn () => $this->service->validateUserBalance($userId2, $amount))
            ->toThrow(InvalidTransferException::class, 'Insufficient balance. Available: 20,00, Required: 50,00');
    });
});
