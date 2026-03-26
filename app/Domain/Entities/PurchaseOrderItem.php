<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class PurchaseOrderItem
{
    private ?int $id = null;
    private int $purchaseOrderId;
    private int $productId;
    private int $quantity;
    private int $quantityReceived;
    private float $unitCost;
    private float $total;
    private ?string $supplierSku;
    private ?string $notes;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        int $purchaseOrderId,
        int $productId,
        int $quantity,
        float $unitCost,
        ?string $supplierSku,
        ?string $notes
    ) {
        $this->purchaseOrderId = $purchaseOrderId;
        $this->productId = $productId;
        $this->quantity = $quantity;
        $this->quantityReceived = 0;
        $this->unitCost = $unitCost;
        $this->total = $quantity * $unitCost;
        $this->supplierSku = $supplierSku;
        $this->notes = $notes;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function getPurchaseOrderId(): int { return $this->purchaseOrderId; }
    public function getProductId(): int { return $this->productId; }
    public function getQuantity(): int { return $this->quantity; }
    public function getQuantityReceived(): int { return $this->quantityReceived; }
    public function getUnitCost(): float { return $this->unitCost; }
    public function getTotal(): float { return $this->total; }
    public function getSupplierSku(): ?string { return $this->supplierSku; }
    public function getNotes(): ?string { return $this->notes; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function addReceived(int $qty): self
    {
        $this->quantityReceived += $qty;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function isFullyReceived(): bool
    {
        return $this->quantityReceived >= $this->quantity;
    }
}
