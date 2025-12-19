<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Transaction\CreateTransactionDTO;
use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(CreateTransactionDTO $dto): Transaction;

    /**
     * @param array<string> $relations
     */
    public function findByIdWithRelations(int $id, array $relations = []): ?Transaction;
}
