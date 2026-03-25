<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;
use InvalidArgumentException;

class Movement
{
    public const TYPE_IN = 'in';
    public const TYPE_OUT = 'out';
    public const TYPE_TRANSFER = 'transfer';
    public const TYPE_ADJUSTMENT = 'adjustment';
    public const TYPE_RETURN = 'return';

    private ?int $id = null;
    private string $uuid;
    private int $productId;
    private int $warehouseId;
    private string $movementType;
    private ?string $referenceType;
    private ?int $referenceId;
    private float $quantity;
    private ?float $unitCost;
    private ?float $totalCost;
    private float $previousQuantity;
    private float $newQuantity;
    private ?string $notes;
    private ?int $createdBy;
    private DateTimeImmutable $createdAt;

    public function __construct(
        string $uuid,
        int $productId,
        int $warehouseId,
        string $movementType,
        float $quantity,
        float $previousQuantity,
        float $newQuantity,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?float $unitCost = null,
        ?string $notes = null,
        ?int $createdBy = null
    ) {
        $this->validateMovementType($movementType);
        $this->uuid = $uuid;
        $this->productId = $productId;
        $this->warehouseId = $warehouseId;
        $this->movementType = $movementType;
        $this->quantity = abs($quantity);
        $this->previousQuantity = $previousQuantity;
        $this->newQuantity = $newQuantity;
        $this->referenceType = $referenceType;
        $this->referenceId = $referenceId;
        $this->unitCost = $unitCost;
        $this->totalCost = ($unitCost !== null) ? $this->quantity * $unitCost : null;
        $this->notes = $notes;
        $this->createdBy = $createdBy;
        $this->createdAt = new DateTimeImmutable();
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

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getWarehouseId(): int
    {
        return $this->warehouseId;
    }

    public function getMovementType(): string
    {
        return $this->movementType;
    }

    public function getReferenceType(): ?string
    {
        return $this->referenceType;
    }

    public function getReferenceId(): ?int
    {
        return $this->referenceId;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getUnitCost(): ?float
    {
        return $this->unitCost;
    }

    public function getTotalCost(): ?float
    {
        return $this->totalCost;
    }

    public function getPreviousQuantity(): float
    {
        return $this->previousQuantity;
    }

    public function getNewQuantity(): float
    {
        return $this->newQuantity;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isInbound(): bool
    {
        return in_array($this->movementType, [self::TYPE_IN, self::TYPE_RETURN]);
    }

    public function isOutbound(): bool
    {
        return in_array($this->movementType, [self::TYPE_OUT]);
    }

    public function isTransfer(): bool
    {
        return $this->movementType === self::TYPE_TRANSFER;
    }

    public function isAdjustment(): bool
    {
        return $this->movementType === self::TYPE_ADJUSTMENT;
    }

    public function getQuantityDifference(): float
    {
        return $this->newQuantity - $this->previousQuantity;
    }

    private function validateMovementType(string $type): void
    {
        $validTypes = [
            self::TYPE_IN,
            self::TYPE_OUT,
            self::TYPE_TRANSFER,
            self::TYPE_ADJUSTMENT,
            self::TYPE_RETURN
        ];

        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException(
                "Invalid movement type: {$type}. Valid types are: " . implode(', ', $validTypes)
            );
        }
    }
}
