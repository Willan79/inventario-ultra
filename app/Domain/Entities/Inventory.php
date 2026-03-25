<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Quantity;
use App\Domain\ValueObjects\Money;
use App\Domain\Exceptions\InsufficientStockException;
use DateTimeImmutable;

class Inventory
{
    private ?int $id = null;
    private int $productId;
    private int $warehouseId;
    private float $quantityAvailable;
    private float $quantityReserved;
    private float $quantityOnOrder;
    private float $averageCost;
    private ?DateTimeImmutable $lastMovementAt;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        int $productId,
        int $warehouseId,
        float $quantityAvailable = 0,
        float $quantityReserved = 0,
        float $quantityOnOrder = 0,
        float $averageCost = 0
    ) {
        $this->productId = $productId;
        $this->warehouseId = $warehouseId;
        $this->quantityAvailable = $quantityAvailable;
        $this->quantityReserved = $quantityReserved;
        $this->quantityOnOrder = $quantityOnOrder;
        $this->averageCost = $averageCost;
        $this->lastMovementAt = null;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getWarehouseId(): int
    {
        return $this->warehouseId;
    }

    public function getQuantityAvailable(): float
    {
        return $this->quantityAvailable;
    }

    public function getQuantityReserved(): float
    {
        return $this->quantityReserved;
    }

    public function getQuantityOnOrder(): float
    {
        return $this->quantityOnOrder;
    }

    public function getAverageCost(): float
    {
        return $this->averageCost;
    }

    public function getLastMovementAt(): ?DateTimeImmutable
    {
        return $this->lastMovementAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getAvailableForSale(): float
    {
        return $this->quantityAvailable - $this->quantityReserved;
    }

    public function canReserve(float $quantity): bool
    {
        return $this->getAvailableForSale() >= $quantity;
    }

    public function reserve(float $quantity): self
    {
        if (!$this->canReserve($quantity)) {
            throw new InsufficientStockException(
                "Cannot reserve {$quantity} units. Only {$this->getAvailableForSale()} available."
            );
        }
        $this->quantityReserved += $quantity;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function releaseReservation(float $quantity): self
    {
        $this->quantityReserved = max(0, $this->quantityReserved - $quantity);
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function addStock(float $quantity, float $unitCost = null): self
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }

        if ($unitCost !== null && $this->averageCost > 0) {
            $totalValue = ($this->quantityAvailable * $this->averageCost) + ($quantity * $unitCost);
            $this->quantityAvailable += $quantity;
            $this->averageCost = $totalValue / $this->quantityAvailable;
        } elseif ($unitCost !== null) {
            $this->quantityAvailable += $quantity;
            $this->averageCost = $unitCost;
        } else {
            $this->quantityAvailable += $quantity;
        }

        $this->lastMovementAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function removeStock(float $quantity): self
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be positive');
        }

        $available = $this->getAvailableForSale();
        if ($quantity > $available) {
            throw new InsufficientStockException(
                "Cannot remove {$quantity} units. Only {$available} available."
            );
        }

        $this->quantityAvailable -= $quantity;
        $this->lastMovementAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function adjustStock(float $newQuantity, string $reason = 'adjustment'): self
    {
        if ($newQuantity < 0) {
            throw new \InvalidArgumentException('Quantity cannot be negative');
        }

        if ($newQuantity < $this->quantityReserved) {
            throw new \InvalidArgumentException('Cannot set quantity below reserved amount');
        }

        $this->quantityAvailable = $newQuantity;
        $this->lastMovementAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function addToOrder(float $quantity): self
    {
        $this->quantityOnOrder += $quantity;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function fulfillOrder(float $quantity): self
    {
        $this->quantityOnOrder = max(0, $this->quantityOnOrder - $quantity);
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function isBelowMinStock(int $minLevel): bool
    {
        return $this->getAvailableForSale() < $minLevel;
    }

    public function isAtReorderPoint(int $reorderPoint): bool
    {
        return $this->getAvailableForSale() <= $reorderPoint;
    }
}
