<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Credit;

interface CreditRepositoryInterface
{
    public function create(array $data): Credit;
}
