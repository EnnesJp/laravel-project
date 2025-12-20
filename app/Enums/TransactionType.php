<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractWithValues;

enum TransactionType: string
{
    use InteractWithValues;

    case DEPOSIT  = 'deposit';
    case TRANSFER = 'transfer';
}
