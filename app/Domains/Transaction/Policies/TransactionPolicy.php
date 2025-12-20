<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Policies;

use App\Domains\User\Models\User;

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
