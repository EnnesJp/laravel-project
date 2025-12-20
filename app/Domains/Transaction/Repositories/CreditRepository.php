<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Repositories;

use App\Domains\Transaction\DTOs\CreateCreditDTO;
use App\Domains\Transaction\Models\Credit;
use App\Domains\Transaction\Repositories\Contracts\CreditRepositoryInterface;

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
