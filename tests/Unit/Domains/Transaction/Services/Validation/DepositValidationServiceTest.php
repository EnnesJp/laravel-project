<?php

declare(strict_types=1);

use App\Domains\Transaction\DTOs\DepositDTO;
use App\Domains\Transaction\Exceptions\InvalidDepositException;
use App\Domains\Transaction\Services\Validation\DepositValidationService;
use App\Domains\User\Enums\UserRole;
use App\Domains\User\Models\User;
use App\Domains\User\Services\UserService;

beforeEach(function () {
    $this->userService = Mockery::mock(UserService::class);
    $this->service     = new DepositValidationService($this->userService);
});

describe('validateDeposit', function () {
    it('throws exception for invalid amount', function () {
        $dto = new DepositDTO(
            amount: 0,
            payee: 2,
            payer: 1
        );

        expect(fn () => $this->service->validateDeposit($dto))
            ->toThrow(InvalidDepositException::class, 'Invalid deposit amount: 0. Amount must be greater than 0');
    });

    it('throws exception for negative amount', function () {
        $dto = new DepositDTO(
            amount: -1000,
            payee: 2,
            payer: 1
        );

        expect(fn () => $this->service->validateDeposit($dto))
            ->toThrow(InvalidDepositException::class, 'Invalid deposit amount: -1000. Amount must be greater than 0');
    });

    it('throws exception when payer and payee are the same', function () {
        $dto = new DepositDTO(
            amount: 10000,
            payee: 1,
            payer: 1
        );

        expect(fn () => $this->service->validateDeposit($dto))
            ->toThrow(InvalidDepositException::class, 'Cannot create deposit where payer and payee are the same user');
    });

    it('throws exception when payer is not found', function () {
        $dto = new DepositDTO(
            amount: 10000,
            payee: 2,
            payer: 999
        );

        $this->userService->shouldReceive('findById')
            ->with(999)
            ->andReturn(null);

        $this->userService->shouldReceive('findById')
            ->with(2)
            ->andReturn(null);

        expect(fn () => $this->service->validateDeposit($dto))
            ->toThrow(InvalidDepositException::class, 'User with ID 999 not found');
    });

    it('throws exception when payee is not found', function () {
        $dto = new DepositDTO(
            amount: 10000,
            payee: 999,
            payer: 1
        );

        $payer       = Mockery::mock(User::class)->makePartial();
        $payer->role = UserRole::EXTERNAL_FOUND->value;
        $payer->shouldReceive('canDeposit')->andReturn(true);

        $this->userService->shouldReceive('findById')
            ->with(1)
            ->andReturn($payer);

        $this->userService->shouldReceive('findById')
            ->with(999)
            ->andReturn(null);

        expect(fn () => $this->service->validateDeposit($dto))
            ->toThrow(InvalidDepositException::class, 'User with ID 999 not found');
    });

    it('throws exception when payer cannot deposit', function () {
        $dto = new DepositDTO(
            amount: 10000,
            payee: 2,
            payer: 1
        );

        $payer       = Mockery::mock(User::class)->makePartial();
        $payer->role = UserRole::USER->value;
        $payer->shouldReceive('canDeposit')->andReturn(false);

        $payee       = Mockery::mock(User::class)->makePartial();
        $payee->role = UserRole::USER->value;
        $payee->shouldReceive('canReciveDeposit')->andReturn(true);

        $this->userService->shouldReceive('findById')
            ->with(1)
            ->andReturn($payer);

        $this->userService->shouldReceive('findById')
            ->with(2)
            ->andReturn($payee);

        expect(fn () => $this->service->validateDeposit($dto))
            ->toThrow(InvalidDepositException::class, "Payer with role 'user' cannot be used for deposits. Only external_found users can be payers");
    });

    it('throws exception when payee cannot receive deposit', function () {
        $dto = new DepositDTO(
            amount: 10000,
            payee: 2,
            payer: 1
        );

        $payer       = Mockery::mock(User::class)->makePartial();
        $payer->role = UserRole::EXTERNAL_FOUND->value;
        $payer->shouldReceive('canDeposit')->andReturn(true);

        $payee       = Mockery::mock(User::class)->makePartial();
        $payee->role = UserRole::SELLER->value;
        $payee->shouldReceive('canReciveDeposit')->andReturn(false);

        $this->userService->shouldReceive('findById')
            ->with(1)
            ->andReturn($payer);

        $this->userService->shouldReceive('findById')
            ->with(2)
            ->andReturn($payee);

        expect(fn () => $this->service->validateDeposit($dto))
            ->toThrow(InvalidDepositException::class, "Payee with role 'seller' cannot recive deposits.");
    });
});
