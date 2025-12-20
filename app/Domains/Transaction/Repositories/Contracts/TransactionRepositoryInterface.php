<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Repositories\Contracts;

use App\Domains\Transaction\DTOs\CreateTransactionDTO;
use App\Domains\Transaction\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function create(CreateTransactionDTO $dto): Transaction;

    /**
     * @param array<string> $relations
     */
    public function findByIdWithRelations(int $transactionId, array $relations = []): ?Transaction;
}
