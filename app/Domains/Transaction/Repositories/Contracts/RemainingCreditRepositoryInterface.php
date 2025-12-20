<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Repositories\Contracts;

use App\Domains\Transaction\Models\RemainingCredit;
use Illuminate\Database\Eloquent\Collection;

interface RemainingCreditRepositoryInterface
{
    /**
     * @return Collection<int, RemainingCredit>
     */
    public function getRemainingCreditsByUserId(int $userId): Collection;
}
