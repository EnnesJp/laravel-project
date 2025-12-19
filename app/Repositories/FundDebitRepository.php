<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Debit;
use App\Models\FundDebit;
use App\Repositories\Contracts\FundDebitRepositoryInterface;

class DebitRepository implements FundDebitRepositoryInterface
{
    public function __construct(
        private readonly FundDebit $model
    ) {
    }

    public function create(array $data): Debit
    {
        return $this->model->create($data);
    }
}
