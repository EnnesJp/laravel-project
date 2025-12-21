<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Repositories;

use App\Domains\Transaction\DTOs\CreateFundDebitDTO;
use App\Domains\Transaction\Models\FundDebit;
use App\Domains\Transaction\Repositories\Contracts\FundDebitRepositoryInterface;

class FundDebitRepository implements FundDebitRepositoryInterface
{
    public function __construct(
        private readonly FundDebit $model
    ) {
    }

    public function create(CreateFundDebitDTO $dto): FundDebit
    {
        return $this->model->create($dto->toArray());
    }
}
