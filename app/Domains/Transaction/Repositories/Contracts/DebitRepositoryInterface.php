<?php

declare(strict_types=1);

namespace App\Domains\Transaction\Repositories\Contracts;

use App\Domains\Transaction\DTOs\CreateDebitDTO;
use App\Domains\Transaction\Models\Debit;
use Illuminate\Support\Collection;

interface DebitRepositoryInterface
{
    public function create(CreateDebitDTO $dto): Debit;

    /**
     * @param Collection<int, CreateDebitDTO> $debits
     */
    public function bulkInsert(Collection $debits): bool;
}
