<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Quantity
{
    public function __construct(
        private float $value,
        private string $unit = 'unit'
    ) {
        if ($value < 0) {
            throw new InvalidArgumentException('Quantity cannot be negative');
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function isZero(): bool
    {
        return $this->value === 0.0;
    }

    public function isLessThan(self $other): bool
    {
        return $this->value < $other->value;
    }

    public function isGreaterThan(self $other): bool
    {
        return $this->value > $other->value;
    }

    public function add(self $other): self
    {
        return new self($this->value + $other->value, $this->unit);
    }

    public function subtract(self $other): self
    {
        $result = $this->value - $other->value;
        if ($result < 0) {
            throw new InvalidArgumentException('Result cannot be negative');
        }
        return new self($result, $this->unit);
    }

    public function __toString(): string
    {
        return "{$this->value} {$this->unit}";
    }
}
