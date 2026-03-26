<?php

declare(strict_types=1);

namespace App\Application\DTOs;

readonly class PurchaseOrderItemDTO
{
    public function __construct(
        public ?int $id,
        public int $purchaseOrderId,
        public int $productId,
        public ?string $productName,
        public ?string $productSku,
        public int $quantity,
        public int $quantityReceived,
        public float $unitCost,
        public float $total,
        public ?string $supplierSku,
        public ?string $notes,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            purchaseOrderId: $data['purchase_order_id'],
            productId: $data['product_id'],
            productName: $data['product_name'] ?? null,
            productSku: $data['product_sku'] ?? null,
            quantity: $data['quantity'],
            quantityReceived: $data['quantity_received'] ?? 0,
            unitCost: (float) ($data['unit_cost'] ?? 0),
            total: (float) ($data['total'] ?? 0),
            supplierSku: $data['supplier_sku'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'purchase_order_id' => $this->purchaseOrderId,
            'product_id' => $this->productId,
            'product_name' => $this->productName,
            'product_sku' => $this->productSku,
            'quantity' => $this->quantity,
            'quantity_received' => $this->quantityReceived,
            'unit_cost' => $this->unitCost,
            'total' => $this->total,
            'supplier_sku' => $this->supplierSku,
            'notes' => $this->notes,
        ];
    }
}
