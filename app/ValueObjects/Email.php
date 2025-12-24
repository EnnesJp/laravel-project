<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

readonly class Email implements JsonSerializable, Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        $cleanValue = $this->clean($value);

        if (!$this->isValid($cleanValue)) {
            throw new InvalidArgumentException("Invalid email: {$value}", 422);
        }

        $this->value = $cleanValue;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLocalPart(): string
    {
        return explode('@', $this->value)[0];
    }

    public function getDomain(): string
    {
        return explode('@', $this->value)[1];
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    private function clean(string $value): string
    {
        return trim(strtolower($value));
    }

    private function isValid(string $value): bool
    {
        if (empty($value)) {
            return false;
        }

        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $parts = explode('@', $value);
        if (count($parts) !== 2) {
            return false;
        }

        [$localPart, $domain] = $parts;

        if (strlen($localPart) > 64) {
            return false;
        }

        if (strlen($domain) > 253) {
            return false;
        }

        if (strpos($value, '..') !== false) {
            return false;
        }

        return true;
    }
}
