<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Enums\UserRole;
use App\Models\User;

trait CreatesUsers
{
    protected function generateCpf(): string
    {
        return str_pad((string) rand(10000000000, 99999999999), 11, '0', STR_PAD_LEFT);
    }

    protected function createUser(array $attributes = []): User
    {
        $defaultAttributes = [
            'document' => $this->generateCpf(),
            'role'     => UserRole::USER,
        ];

        return User::factory()->create(array_merge($defaultAttributes, $attributes));
    }

    protected function createAdmin(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role' => UserRole::ADMIN], $attributes));
    }

    protected function createExternalFund(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role' => UserRole::EXTERNAL_FOUND], $attributes));
    }

    protected function createSeller(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role' => UserRole::SELLER], $attributes));
    }

    protected function createRegularUser(array $attributes = []): User
    {
        return $this->createUser(array_merge(['role' => UserRole::USER], $attributes));
    }
}
