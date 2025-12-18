<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\User\CreateUserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }

    public function createUser(CreateUserDTO $dto): User
    {
        $hash = Hash::make($dto->password);

        return $this->userRepository->create(CreateUserDTO::fromArray([
            ...$dto->toArray(),
            'password' => $hash,
        ]));
    }
}
