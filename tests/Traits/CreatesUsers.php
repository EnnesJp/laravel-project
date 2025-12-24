<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Domains\Transaction\Enums\TransactionType;
use App\Domains\Transaction\Models\Credit;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\User\Enums\UserRole;
use App\Domains\User\Models\User;
use App\ValueObjects\Document\Cpf;

trait CreatesUsers
{
    protected function generateCpf(): string
    {
        return Cpf::generate()->getValue();
    }

    protected function createUser(array $attributes = []): User
    {
        $defaultAttributes = [
            'document' => $this->generateCpf(),
            'role'     => UserRole::USER->value,
        ];

        return User::factory()->create(array_merge($defaultAttributes, $attributes));
    }

    protected function createAdmin(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role' => UserRole::ADMIN->value], $attributes));
    }

    protected function createExternalFund(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role' => UserRole::EXTERNAL_FOUND->value], $attributes));
    }

    protected function createSeller(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role' => UserRole::SELLER->value], $attributes));
    }

    protected function createRegularUser(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role' => UserRole::USER->value], $attributes));
    }

    protected function createUserWithBalance(UserRole $role, int $balance): \App\Domains\User\Models\User
    {
        $user         = $this->createUser(['role' => $role->value]);
        $externalFund = $this->createExternalFund();

        $transaction = Transaction::create([
            'payer_user_id' => $externalFund->id,
            'payee_user_id' => $user->id,
            'type'          => TransactionType::DEPOSIT,
        ]);

        Credit::create([
            'transaction_id' => $transaction->id,
            'amount'         => $balance * 100,
        ]);

        return $user;
    }
}
