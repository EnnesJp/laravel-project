<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Repositories;

use App\Domains\Transaction\DTOs\CreateTransactionDTO;
use App\Domains\Transaction\Models\Transaction;
use App\Domains\Transaction\Repositories\Contracts\TransactionRepositoryInterface;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private readonly Transaction $model
    ) {
    }

    public function create(CreateTransactionDTO $dto): Transaction
    {
        return $this->model->create($dto->toArray());
    }

    public function findByIdWithRelations(int $transactionId, array $relations = []): ?Transaction
    {
        return $this->model->with($relations)->find($transactionId);
    }
}
