<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\CreateUserRequest;

class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $document,
        public readonly string $password,
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
        );
    }

    public function toArray(): array
    {
        return [
            'name'     => $this->name,
            'email'    => $this->email,
            'document' => $this->document,
            'password' => $this->password,
        ];
    }
}
