<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class SKU
{
    public function __construct(
        private string $value
    ) {
        if (strlen($value) < 3 || strlen($value) > 50) {
            throw new InvalidArgumentException('SKU must be between 3 and 50 characters');
        }
        if (!preg_match('/^[A-Z0-9\-]+$/i', $value)) {
            throw new InvalidArgumentException('SKU must contain only alphanumeric characters and hyphens');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return strtoupper($this->value) === strtoupper($other->value);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
