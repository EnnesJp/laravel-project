<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Credit;
use App\Repositories\Contracts\CreditRepositoryInterface;

class CreditRepository implements CreditRepositoryInterface
{
    public function __construct(
        private readonly Credit $model
    ) {
    }

    public function create(array $data): Credit
    {
        return $this->model->create($data);
    }
}
