<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\DTOs\User\CreateUserDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(CreateUserDTO $data): User;
}
