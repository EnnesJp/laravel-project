<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\Transaction\CreateDebitDTO;
use App\Models\Debit;
use Illuminate\Support\Collection;

interface DebitRepositoryInterface
{
    public function create(CreateDebitDTO $dto): Debit;

    /**
     * @param Collection<CreateDebitDTO> $debits
     */
    public function bulkInsert(Collection $debits): bool;
}
