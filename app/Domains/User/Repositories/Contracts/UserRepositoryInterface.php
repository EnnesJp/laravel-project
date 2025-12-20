<?php

declare(strict_types=1);

namespace App\Domains\User\Repositories\Contracts;

use App\Domains\User\DTOs\CreateUserDTO;
use App\Domains\User\Models\User;

interface UserRepositoryInterface
{
    public function find(int $userId): ?User;

    public function create(CreateUserDTO $data): User;
}
