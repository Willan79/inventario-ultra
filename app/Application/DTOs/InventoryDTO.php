<?php

declare(strict_types=1);

namespace App\Application\DTOs;

readonly class InventoryDTO
{
    public function __construct(
        public ?int $id,
        public int $productId,
        public int $warehouseId,
        public float $quantityAvailable,
        public float $quantityReserved,
        public float $quantityOnOrder,
        public float $averageCost,
        public ?string $productName = null,
        public ?string $productSku = null,
        public ?string $warehouseName = null,
        public ?string $warehouseCode = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            productId: $data['product_id'],
            warehouseId: $data['warehouse_id'],
            quantityAvailable: (float) ($data['quantity_available'] ?? 0),
            quantityReserved: (float) ($data['quantity_reserved'] ?? 0),
            quantityOnOrder: (float) ($data['quantity_on_order'] ?? 0),
            averageCost: (float) ($data['average_cost'] ?? 0),
            productName: $data['product_name'] ?? null,
            productSku: $data['product_sku'] ?? null,
            warehouseName: $data['warehouse_name'] ?? null,
            warehouseCode: $data['warehouse_code'] ?? null,
        );
    }

    public function getAvailableForSale(): float
    {
        return $this->quantityAvailable - $this->quantityReserved;
    }

    public function getTotalValue(): float
    {
        return $this->quantityAvailable * $this->averageCost;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->productId,
            'warehouse_id' => $this->warehouseId,
            'quantity_available' => $this->quantityAvailable,
            'quantity_reserved' => $this->quantityReserved,
            'quantity_on_order' => $this->quantityOnOrder,
            'average_cost' => $this->averageCost,
            'available_for_sale' => $this->getAvailableForSale(),
            'total_value' => $this->getTotalValue(),
            'product_name' => $this->productName,
            'product_sku' => $this->productSku,
            'warehouse_name' => $this->warehouseName,
            'warehouse_code' => $this->warehouseCode,
        ];
    }
}
