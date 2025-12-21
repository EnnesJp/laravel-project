<?php

declare(strict_types=1);

namespace Tests\Traits;

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
}
