<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use DateTimeImmutable;

class PurchaseOrder
{
    private ?int $id = null;
    private string $uuid;
    private string $orderNumber;
    private int $supplierId;
    private string $status;
    private DateTimeImmutable $orderDate;
    private ?DateTimeImmutable $expectedDate;
    private ?DateTimeImmutable $receivedDate;
    private float $subtotal;
    private float $taxAmount;
    private float $total;
    private ?string $notes;
    private int $createdBy;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $uuid,
        string $orderNumber,
        int $supplierId,
        string $status,
        DateTimeImmutable $orderDate,
        ?DateTimeImmutable $expectedDate,
        ?DateTimeImmutable $receivedDate,
        float $subtotal,
        float $taxAmount,
        float $total,
        ?string $notes,
        int $createdBy
    ) {
        $this->uuid = $uuid;
        $this->orderNumber = $orderNumber;
        $this->supplierId = $supplierId;
        $this->status = $status;
        $this->orderDate = $orderDate;
        $this->expectedDate = $expectedDate;
        $this->receivedDate = $receivedDate;
        $this->subtotal = $subtotal;
        $this->taxAmount = $taxAmount;
        $this->total = $total;
        $this->notes = $notes;
        $this->createdBy = $createdBy;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function setId(int $id): self { $this->id = $id; return $this; }
    public function getUuid(): string { return $this->uuid; }
    public function getOrderNumber(): string { return $this->orderNumber; }
    public function getSupplierId(): int { return $this->supplierId; }
    public function getStatus(): string { return $this->status; }
    public function getOrderDate(): DateTimeImmutable { return $this->orderDate; }
    public function getExpectedDate(): ?DateTimeImmutable { return $this->expectedDate; }
    public function getReceivedDate(): ?DateTimeImmutable { return $this->receivedDate; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getTaxAmount(): float { return $this->taxAmount; }
    public function getTotal(): float { return $this->total; }
    public function getNotes(): ?string { return $this->notes; }
    public function getCreatedBy(): int { return $this->createdBy; }
    public function getCreatedAt(): DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): DateTimeImmutable { return $this->updatedAt; }

    public function updateStatus(string $status): self
    {
        $this->status = $status;
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }

    public function markAsReceived(): self
    {
        $this->status = 'received';
        $this->receivedDate = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        return $this;
    }
}
