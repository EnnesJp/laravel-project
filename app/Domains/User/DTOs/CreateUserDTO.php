<?php

declare(strict_types=1);

namespace App\Domains\User\DTOs;

use App\Http\Requests\CreateUserRequest;
use App\ValueObjects\Document\Base\Document;
use App\ValueObjects\Document\Factory\DocumentFactory;
use App\ValueObjects\Email;
use App\ValueObjects\Password;

class CreateUserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly Email $email,
        public readonly Document $document,
        public readonly Password $password,
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
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: Email::fromString($data['email']),
            document: DocumentFactory::create($data['document']),
            password: Password::fromString($data['password']),
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
            'email'    => $this->email->getValue(),
            'document' => $this->document->getValue(),
            'password' => $this->password->getValue(),
            'role'     => $this->role,
        ];
    }
}
