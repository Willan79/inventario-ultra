<?php

declare(strict_types=1);

namespace App\Application\DTOs;

readonly class MovementDTO
{
    public function __construct(
        public ?int $id,
        public string $uuid,
        public int $productId,
        public int $warehouseId,
        public string $movementType,
        public float $quantity,
        public float $previousQuantity,
        public float $newQuantity,
        public ?string $referenceType = null,
        public ?int $referenceId = null,
        public ?float $unitCost = null,
        public ?float $totalCost = null,
        public ?string $notes = null,
        public ?int $createdBy = null,
        public ?string $createdAt = null,
        public ?string $productName = null,
        public ?string $productSku = null,
        public ?string $warehouseName = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            uuid: $data['uuid'],
            productId: $data['product_id'],
            warehouseId: $data['warehouse_id'],
            movementType: $data['movement_type'],
            quantity: (float) $data['quantity'],
            previousQuantity: (float) $data['previous_quantity'],
            newQuantity: (float) $data['new_quantity'],
            referenceType: $data['reference_type'] ?? null,
            referenceId: $data['reference_id'] ?? null,
            unitCost: isset($data['unit_cost']) ? (float) $data['unit_cost'] : null,
            totalCost: isset($data['total_cost']) ? (float) $data['total_cost'] : null,
            notes: $data['notes'] ?? null,
            createdBy: $data['created_by'] ?? null,
            createdAt: $data['created_at'] ?? null,
            productName: $data['product_name'] ?? null,
            productSku: $data['product_sku'] ?? null,
            warehouseName: $data['warehouse_name'] ?? null,
        );
    }

    public function getQuantityDifference(): float
    {
        return $this->newQuantity - $this->previousQuantity;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'product_id' => $this->productId,
            'warehouse_id' => $this->warehouseId,
            'movement_type' => $this->movementType,
            'quantity' => $this->quantity,
            'previous_quantity' => $this->previousQuantity,
            'new_quantity' => $this->newQuantity,
            'quantity_difference' => $this->getQuantityDifference(),
            'reference_type' => $this->referenceType,
            'reference_id' => $this->referenceId,
            'unit_cost' => $this->unitCost,
            'total_cost' => $this->totalCost,
            'notes' => $this->notes,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
            'product_name' => $this->productName,
            'product_sku' => $this->productSku,
            'warehouse_name' => $this->warehouseName,
        ];
    }
}
