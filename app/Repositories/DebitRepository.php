<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Transaction\CreateDebitDTO;
use App\Models\Debit;
use App\Repositories\Contracts\DebitRepositoryInterface;
use Illuminate\Support\Collection;

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

    public function bulkInsert(Collection $debits): bool
    {
        $data = $debits->map(function (CreateDebitDTO $dto) {
            $array               = $dto->toArray();
            $now                 = now();
            $array['created_at'] = $now;
            $array['updated_at'] = $now;
            return $array;
        })->toArray();

        return $this->model->insert($data);
    }
}
