<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Location
{
    public function __construct(
        private string $warehouseCode,
        private string $zone,
        private ?string $aisle = null,
        private ?string $shelf = null,
        private ?string $bin = null
    ) {
        if (empty($warehouseCode) || empty($zone)) {
            throw new InvalidArgumentException('Warehouse code and zone are required');
        }
    }

    public function getWarehouseCode(): string
    {
        return $this->warehouseCode;
    }

    public function getZone(): string
    {
        return $this->zone;
    }

    public function getAisle(): ?string
    {
        return $this->aisle;
    }

    public function getShelf(): ?string
    {
        return $this->shelf;
    }

    public function getBin(): ?string
    {
        return $this->bin;
    }

    public function getFullLocation(): string
    {
        $parts = [$this->warehouseCode, $this->zone];
        if ($this->aisle) $parts[] = $this->aisle;
        if ($this->shelf) $parts[] = $this->shelf;
        if ($this->bin) $parts[] = $this->bin;
        return implode('-', $parts);
    }

    public function __toString(): string
    {
        return $this->getFullLocation();
    }
}
