<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Repositories\Contracts;

use App\Domains\Transaction\DTOs\CreateCreditDTO;
use App\Domains\Transaction\Models\Credit;

interface CreditRepositoryInterface
{
    public function create(CreateCreditDTO $dto): Credit;
}
