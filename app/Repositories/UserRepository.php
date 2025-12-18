<?php

declare(strict_types=1);

namespace App\Repositories;

use App\DTOs\User\CreateUserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly User $model
    ) {
    }

    public function create(CreateUserDTO $data): User
    {
        return $this->model->create($data->toArray());
    }
}
