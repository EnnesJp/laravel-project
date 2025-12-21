<?php

declare(strict_types=1);

namespace App\Domains\User\Services;

use App\Domains\User\DTOs\CreateUserDTO;
use App\Domains\User\DTOs\LoginDTO;
use App\Domains\User\Models\User;
use App\Domains\User\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

    /**
     * @return array<string, mixed>
     * @throws ValidationException
     */
    public function login(LoginDTO $dto): array
    {
        if (!Auth::attempt(['email' => $dto->email, 'password' => $dto->password])) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user();
        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function findById(int $userId): ?User
    {
        return $this->userRepository->find($userId);
    }
}
