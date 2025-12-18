<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\CreateUserDTO;
use App\Enums\UserRole;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {
    }


    /**
     * Create a new user from DTO
     */
    public function createUser(CreateUserDTO $dto): User
    {
        $userData = [
            'name'     => $dto->name,
            'email'    => $dto->email,
            'document' => $dto->document,
            'password' => Hash::make($dto->password),
            'role'     => UserRole::USER,
        ];

        return $this->userRepository->create($userData);
    }
}
