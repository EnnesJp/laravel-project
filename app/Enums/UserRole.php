<?php

declare(strict_types=1);

namespace App\Enums;

use App\Concerns\InteractWithValues;

enum UserRole: string
{
    use InteractWithValues;

    case ADMIN          = 'admin';
    case USER           = 'user';
    case SELLER         = 'seller';
    case EXTERNAL_FOUND = 'external_found';
}
