<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\SKU;
use DateTimeImmutable;
use InvalidArgumentException;

class Product
{
    private ?int $id = null;
    private string $uuid;
    private SKU $sku;
    private string $name;
    private ?string $description;
    private ?int $categoryId;
    private string $unitOfMeasure;
    private ?string $barcode;
    private bool $isActive;
    private int $minStockLevel;
    private ?int $maxStockLevel;
    private int $reorderPoint;
    private string $costMethod;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;
    private ?DateTimeImmutable $deletedAt = null;

    public function __construct(
        string $uuid,
        SKU $sku,
        string $name,
        ?string $description = null,
        ?int $categoryId = null,
        string $unitOfMeasure = 'unit',
        ?string $barcode = null,
        bool $isActive = true,
        int $minStockLevel = 0,
        ?int $maxStockLevel = null,
        int $reorderPoint = 0,
        string $costMethod = 'average'
    ) {
        $this->uuid = $uuid;
        $this->sku = $sku;
        $this->name = $name;
        $this->description = $description;
        $this->categoryId = $categoryId;
        $this->unitOfMeasure = $unitOfMeasure;
        $this->barcode = $barcode;
        $this->isActive = $isActive;
        $this->minStockLevel = $minStockLevel;
        $this->maxStockLevel = $maxStockLevel;
        $this->reorderPoint = $reorderPoint;
        $this->costMethod = $costMethod;
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

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getSku(): SKU
    {
        return $this->sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }

    public function getUnitOfMeasure(): string
    {
        return $this->unitOfMeasure;
    }

    public function getBarcode(): ?string
    {
        return $this->barcode;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getMinStockLevel(): int
    {
        return $this->minStockLevel;
    }

    public function getMaxStockLevel(): ?int
    {
        return $this->maxStockLevel;
    }

    public function getReorderPoint(): int
    {
        return $this->reorderPoint;
    }

    public function getCostMethod(): string
    {
        return $this->costMethod;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function update(
        ?string $name = null,
        ?string $description = null,
        ?int $categoryId = null,
        ?string $unitOfMeasure = null,
        ?string $barcode = null,
        ?bool $isActive = null,
        ?int $minStockLevel = null,
        ?int $maxStockLevel = null,
        ?int $reorderPoint = null
    ): self {
        if ($name !== null) $this->name = $name;
        if ($description !== null) $this->description = $description;
        if ($categoryId !== null) $this->categoryId = $categoryId;
        if ($unitOfMeasure !== null) $this->unitOfMeasure = $unitOfMeasure;
        if ($barcode !== null) $this->barcode = $barcode;
        if ($isActive !== null) $this->isActive = $isActive;
        if ($minStockLevel !== null) $this->minStockLevel = $minStockLevel;
        if ($maxStockLevel !== null) $this->maxStockLevel = $maxStockLevel;
        if ($reorderPoint !== null) $this->reorderPoint = $reorderPoint;
        
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function deactivate(): self
    {
        $this->isActive = false;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function activate(): self
    {
        $this->isActive = true;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function softDelete(): self
    {
        $this->deletedAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function needsReorder(int $currentStock): bool
    {
        return $currentStock <= $this->reorderPoint;
    }

    public function isBelowMinStock(int $currentStock): bool
    {
        return $currentStock < $this->minStockLevel;
    }
}
