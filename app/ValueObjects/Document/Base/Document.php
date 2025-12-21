<?php

declare(strict_types=1);

namespace App\ValueObjects\Document\Base;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

abstract readonly class Document implements JsonSerializable, Stringable
{
    protected readonly string $value;

    final public function __construct(string $value)
    {
        $cleanValue = $this->clean($value);

        if (!$this->isValid($cleanValue)) {
            throw new InvalidArgumentException("Invalid {$this->getType()}: {$value}");
        }

        $this->value = $cleanValue;
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getFormatted(): string
    {
        return $this->format($this->value);
    }

    public function equals(Document $other): bool
    {
        return $this->value === $other->value && get_class($this) === get_class($other);
    }

    public function __toString(): string
    {
        return $this->getFormatted();
    }

    public function jsonSerialize(): string
    {
        return $this->getFormatted();
    }

    protected function clean(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }

    abstract protected function isValid(string $value): bool;
    abstract protected function format(string $value): string;
    abstract protected function getType(): string;
}
