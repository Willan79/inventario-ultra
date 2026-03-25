<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        private float $amount,
        private string $currency = 'USD'
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative');
        }
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add(self $other): self
    {
        $this->ensureSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->ensureSameCurrency($other);
        $result = $this->amount - $other->amount;
        if ($result < 0) {
            throw new InvalidArgumentException('Result cannot be negative');
        }
        return new self($result, $this->currency);
    }

    public function multiply(float $factor): self
    {
        return new self($this->amount * $factor, $this->currency);
    }

    public function __toString(): string
    {
        return "{$this->currency} {$this->amount}";
    }

    private function ensureSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException('Cannot operate with different currencies');
        }
    }
}
