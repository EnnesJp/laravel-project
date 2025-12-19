<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Transaction\CreateCreditDTO;
use App\Models\Credit;

interface CreditRepositoryInterface
{
    public function create(CreateCreditDTO $dto): Credit;
}
