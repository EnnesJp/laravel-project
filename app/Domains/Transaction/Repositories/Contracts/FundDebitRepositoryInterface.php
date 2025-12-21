<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Repositories\Contracts;

use App\Domains\Transaction\DTOs\CreateFundDebitDTO;
use App\Domains\Transaction\Models\FundDebit;

interface FundDebitRepositoryInterface
{
    public function create(CreateFundDebitDTO $dto): FundDebit;
}
