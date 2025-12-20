<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Transaction\CreateCreditDTO;
use App\Models\Credit;
use App\Repositories\Contracts\CreditRepositoryInterface;

class CreditRepository implements CreditRepositoryInterface
{
    public function __construct(
        private readonly Credit $model
    ) {
    }

    public function create(CreateCreditDTO $dto): Credit
    {
        return $this->model->create($dto->toArray());
    }
}
