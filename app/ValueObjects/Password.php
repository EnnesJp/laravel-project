<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

readonly class Password implements JsonSerializable, Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        if (!$this->isValid($value)) {
            throw new InvalidArgumentException('Password must be at least 8 characters long and contain letters, numbers, and symbols.', 422);
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getHashed(): string
    {
        return password_hash($this->value, PASSWORD_DEFAULT);
    }

    public function equals(Password $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return '***';
    }

    private function isValid(string $value): bool
    {
        if (strlen($value) < 8) {
            return false;
        }

        if (!preg_match('/[a-zA-Z]/', $value)) {
            return false;
        }

        if (!preg_match('/[0-9]/', $value)) {
            return false;
        }

        if (!preg_match('/[^a-zA-Z0-9]/', $value)) {
            return false;
        }

        return true;
    }
}
