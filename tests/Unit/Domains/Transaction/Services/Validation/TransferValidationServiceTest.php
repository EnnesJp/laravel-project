<?php

declare(strict_types=1);

use App\Domains\Transaction\DTOs\TransferDTO;
use App\Domains\Transaction\Exceptions\InvalidTransferException;
use App\Domains\Transaction\Services\Validation\TransferValidationService;
use App\Domains\User\Enums\UserRole;
use App\Domains\User\Models\User;
use App\Domains\User\Services\UserService;

beforeEach(function () {
    $this->userService = Mockery::mock(UserService::class);
    $this->service     = new TransferValidationService($this->userService);
});

describe('validateTransferData', function () {
    it('throws exception for invalid amount', function () {
        $dto = new TransferDTO(
            amount: 0,
            payee: 2,
            payer: 1
        );

        expect(fn () => $this->service->validateTransferData($dto, 1))
            ->toThrow(InvalidTransferException::class, 'Invalid deposit amount: 0. Amount must be greater than 0');
    });

    it('throws exception for negative amount', function () {
        $dto = new TransferDTO(
            amount: -1000,
            payee: 2,
            payer: 1
        );

        expect(fn () => $this->service->validateTransferData($dto, 1))
            ->toThrow(InvalidTransferException::class, 'Invalid deposit amount: -1000. Amount must be greater than 0');
    });

    it('throws exception when payer and payee are the same', function () {
        $dto = new TransferDTO(
            amount: 10000,
            payee: 1,
            payer: 1
        );

        expect(fn () => $this->service->validateTransferData($dto, 1))
            ->toThrow(InvalidTransferException::class, 'Cannot create deposit where payer and payee are the same user');
    });

    it('throws exception when user tries to transfer for others', function () {
        $dto = new TransferDTO(
            amount: 10000,
            payee: 3,
            payer: 2
        );

        $currentUser       = Mockery::mock(User::class)->makePartial();
        $currentUser->id   = 1;
        $currentUser->role = UserRole::USER->value;

        $this->userService->shouldReceive('findById')
            ->with(1)
            ->andReturn($currentUser);

        expect(fn () => $this->service->validateTransferData($dto, 1))
            ->toThrow(InvalidTransferException::class, "Users with 'user' role can only transfer their own money");
    });

    it('throws exception when payer is not found', function () {
        $dto = new TransferDTO(
            amount: 10000,
            payee: 2,
            payer: 999
        );

        $currentUser       = Mockery::mock(User::class)->makePartial();
        $currentUser->id   = 999;
        $currentUser->role = UserRole::USER->value;

        $this->userService->shouldReceive('findById')
            ->with(999)
            ->andReturn($currentUser, null);

        $this->userService->shouldReceive('findById')
            ->with(2)
            ->andReturn(null);

        expect(fn () => $this->service->validateTransferData($dto, 999))
            ->toThrow(InvalidTransferException::class, 'User with ID 999 not found');
    });

    it('throws exception when payee is not found', function () {
        $dto = new TransferDTO(
            amount: 10000,
            payee: 999,
            payer: 1
        );

        $currentUser       = Mockery::mock(User::class)->makePartial();
        $currentUser->id   = 1;
        $currentUser->role = UserRole::USER->value;

        $payer       = Mockery::mock(User::class)->makePartial();
        $payer->role = UserRole::USER->value;
        $payer->shouldReceive('canTransfer')->andReturn(true);

        $this->userService->shouldReceive('findById')
            ->with(1)
            ->andReturn($currentUser, $payer);

        $this->userService->shouldReceive('findById')
            ->with(999)
            ->andReturn(null);

        expect(fn () => $this->service->validateTransferData($dto, 1))
            ->toThrow(InvalidTransferException::class, 'User with ID 999 not found');
    });

    it('throws exception when payer cannot transfer', function () {
        $dto = new TransferDTO(
            amount: 10000,
            payee: 2,
            payer: 1
        );

        $currentUser       = Mockery::mock(User::class)->makePartial();
        $currentUser->id   = 1;
        $currentUser->role = UserRole::USER->value;

        $payer       = Mockery::mock(User::class)->makePartial();
        $payer->role = UserRole::EXTERNAL_FOUND->value;
        $payer->shouldReceive('canTransfer')->andReturn(false);

        $payee       = Mockery::mock(User::class)->makePartial();
        $payee->role = UserRole::USER->value;
        $payee->shouldReceive('canReciveTransfer')->andReturn(true);

        $this->userService->shouldReceive('findById')
            ->with(1)
            ->andReturn($currentUser, $payer);

        $this->userService->shouldReceive('findById')
            ->with(2)
            ->andReturn($payee);

        expect(fn () => $this->service->validateTransferData($dto, 1))
            ->toThrow(InvalidTransferException::class, "User with role 'external_found' cannot perform transfers");
    });

    it('throws exception when payee cannot receive transfer', function () {
        $dto = new TransferDTO(
            amount: 10000,
            payee: 2,
            payer: 1
        );

        $currentUser       = Mockery::mock(User::class)->makePartial();
        $currentUser->id   = 1;
        $currentUser->role = UserRole::USER->value;

        $payer       = Mockery::mock(User::class)->makePartial();
        $payer->role = UserRole::USER->value;
        $payer->shouldReceive('canTransfer')->andReturn(true);

        $payee       = Mockery::mock(User::class)->makePartial();
        $payee->role = UserRole::EXTERNAL_FOUND->value;
        $payee->shouldReceive('canReciveTransfer')->andReturn(false);

        $this->userService->shouldReceive('findById')
            ->with(1)
            ->andReturn($currentUser, $payer);

        $this->userService->shouldReceive('findById')
            ->with(2)
            ->andReturn($payee);

        expect(fn () => $this->service->validateTransferData($dto, 1))
            ->toThrow(InvalidTransferException::class, "User with role 'external_found' cannot recive transfers");
    });
});

describe('permission edge cases', function () {
    it('throws exception when seller tries to transfer', function () {
        $dto = new TransferDTO(
            amount: 10000,
            payee: 2,
            payer: 1
        );

        $currentUser       = Mockery::mock(User::class)->makePartial();
        $currentUser->id   = 1;
        $currentUser->role = UserRole::SELLER->value;

        $payer       = Mockery::mock(User::class)->makePartial();
        $payer->role = UserRole::SELLER->value;
        $payer->shouldReceive('canTransfer')->andReturn(false);

        $payee       = Mockery::mock(User::class)->makePartial();
        $payee->role = UserRole::USER->value;
        $payee->shouldReceive('canReciveTransfer')->andReturn(true);

        $this->userService->shouldReceive('findById')
            ->with(1)
            ->andReturn($currentUser, $payer);

        $this->userService->shouldReceive('findById')
            ->with(2)
            ->andReturn($payee);

        expect(fn () => $this->service->validateTransferData($dto, 1))
            ->toThrow(InvalidTransferException::class, "User with role 'seller' cannot perform transfers");
    });
});
