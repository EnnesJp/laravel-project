<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Debit;

interface FundDebitRepositoryInterface
{
    public function create(array $data): Debit;
}
