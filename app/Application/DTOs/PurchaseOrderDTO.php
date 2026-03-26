<?php

declare(strict_types=1);

namespace App\Application\DTOs;

readonly class PurchaseOrderDTO
{
    public function __construct(
        public ?int $id,
        public string $uuid,
        public string $orderNumber,
        public int $supplierId,
        public ?string $supplierName,
        public string $status,
        public string $orderDate,
        public ?string $expectedDate,
        public ?string $receivedDate,
        public float $subtotal,
        public float $taxAmount,
        public float $total,
        public ?string $notes,
        public int $createdBy,
        public ?string $createdByName,
        public int $itemsCount = 0,
        public int $totalItems = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            uuid: $data['uuid'],
            orderNumber: $data['order_number'],
            supplierId: $data['supplier_id'],
            supplierName: $data['supplier_name'] ?? null,
            status: $data['status'],
            orderDate: $data['order_date'],
            expectedDate: $data['expected_date'] ?? null,
            receivedDate: $data['received_date'] ?? null,
            subtotal: (float) ($data['subtotal'] ?? 0),
            taxAmount: (float) ($data['tax_amount'] ?? 0),
            total: (float) ($data['total'] ?? 0),
            notes: $data['notes'] ?? null,
            createdBy: $data['created_by'],
            createdByName: $data['created_by_name'] ?? null,
            itemsCount: $data['items_count'] ?? 0,
            totalItems: $data['total_items'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'order_number' => $this->orderNumber,
            'supplier_id' => $this->supplierId,
            'supplier_name' => $this->supplierName,
            'status' => $this->status,
            'order_date' => $this->orderDate,
            'expected_date' => $this->expectedDate,
            'received_date' => $this->receivedDate,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->taxAmount,
            'total' => $this->total,
            'notes' => $this->notes,
            'created_by' => $this->createdBy,
            'created_by_name' => $this->createdByName,
            'items_count' => $this->itemsCount,
            'total_items' => $this->totalItems,
        ];
    }
}
