<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Transaction\CreateFundDebitDTO;
use App\Models\FundDebit;

interface FundDebitRepositoryInterface
{
    public function create(CreateFundDebitDTO $dto): FundDebit;
}
