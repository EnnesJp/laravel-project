<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Repositories;

use App\Domains\Transaction\Models\RemainingCredit;
use App\Domains\Transaction\Repositories\Contracts\RemainingCreditRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RemainingCreditRepository implements RemainingCreditRepositoryInterface
{
    public function __construct(
        private readonly RemainingCredit $model
    ) {
    }

    /**
     * @return Collection<int, RemainingCredit>
     */
    public function getRemainingCreditsByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
