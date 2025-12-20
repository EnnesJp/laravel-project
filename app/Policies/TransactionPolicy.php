<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class TransactionPolicy
{
    public function deposit(User $user): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::EXTERNAL_FOUND]);
    }

    public function transfer(User $user): bool
    {
        return in_array($user->role, [UserRole::ADMIN, UserRole::USER, UserRole::SELLER]);
    }
}
