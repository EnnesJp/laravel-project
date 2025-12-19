<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\Transaction\CreateFundDebitDTO;
use App\Models\Debit;
use App\Models\FundDebit;
use App\Repositories\Contracts\FundDebitRepositoryInterface;

class FundDebitRepository implements FundDebitRepositoryInterface
{
    public function __construct(
        private readonly FundDebit $model
    ) {
    }

    public function create(CreateFundDebitDTO $dto): Debit
    {
        return $this->model->create($dto->toArray());
    }
}
