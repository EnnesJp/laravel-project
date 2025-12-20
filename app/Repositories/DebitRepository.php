<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Transaction\CreateDebitDTO;
use App\Models\Debit;
use App\Repositories\Contracts\DebitRepositoryInterface;

class DebitRepository implements DebitRepositoryInterface
{
    public function __construct(
        private readonly Debit $model
    ) {
    }

    public function create(CreateDebitDTO $dto): Debit
    {
        return $this->model->create($dto->toArray());
    }
}
