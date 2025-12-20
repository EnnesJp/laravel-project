<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\RemainingCredit;
use App\Repositories\Contracts\RemainingCreditRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class RemainingCreditRepository implements RemainingCreditRepositoryInterface
{
    public function __construct(
        private readonly RemainingCredit $model
    ) {
    }

    public function getRemainingCreditsByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }
}
