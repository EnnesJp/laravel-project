<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Transaction\CreateDebitDTO;
use App\Models\Debit;

interface DebitRepositoryInterface
{
    public function create(CreateDebitDTO $dto): Debit;
}
