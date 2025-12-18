<?php

declare(strict_types=1);

namespace App\DTOs\User;

use App\Http\Requests\CreateUserRequest;

class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $document,
        public readonly string $password,
        public readonly string $role
    ) {
    }

    public static function fromRequest(CreateUserRequest $request): self
    {
        $validated = $request->validated();

        return new self(
            name: $validated['name'],
            email: $validated['email'],
            document: $validated['document'],
            password: $validated['password'],
            role: $validated['role']
        );
    }

    /**
     * @param array<string, string> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            document: $data['document'],
            password: $data['password'],
            role: $data['role']
        );
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'name'     => $this->name,
            'email'    => $this->email,
            'document' => $this->document,
            'password' => $this->password,
            'role'     => $this->role,
        ];
    }
}
