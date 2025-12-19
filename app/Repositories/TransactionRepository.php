<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private readonly Transaction $model
    ) {
    }

    public function create(array $data): Transaction
    {
        return $this->model->create($data);
    }

    public function findByIdWithRelations(int $id, array $relations = []): ?Transaction
    {
        return $this->model->with($relations)->find($id);
    }
}
