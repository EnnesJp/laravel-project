<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class TransactionPolicy
{
    public function deposit(User $user): bool
    {
        return $user->canDeposit();
    }

    public function transfer(User $user): bool
    {
        return $user->canTransfer();
    }
}
