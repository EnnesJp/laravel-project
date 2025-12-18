<?php

declare(strict_types=1);

namespace App\DTOs;

use App\Http\Requests\CreateUserRequest;

readonly class CreateUserDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $document,
        public string $password,
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
