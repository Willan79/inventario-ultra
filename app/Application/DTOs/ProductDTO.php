<?php

declare(strict_types=1);

namespace App\Application\DTOs;

readonly class ProductDTO
{
    public function __construct(
        public ?int $id,
        public string $uuid,
        public string $sku,
        public string $name,
        public ?string $description,
        public ?int $categoryId,
        public string $unitOfMeasure,
        public ?string $barcode,
        public bool $isActive,
        public int $minStockLevel,
        public ?int $maxStockLevel,
        public int $reorderPoint,
        public string $costMethod,
        public ?string $categoryName = null,
        public ?float $totalStock = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            uuid: $data['uuid'],
            sku: $data['sku'],
            name: $data['name'],
            description: $data['description'] ?? null,
            categoryId: $data['category_id'] ?? null,
            unitOfMeasure: $data['unit_of_measure'] ?? 'unit',
            barcode: $data['barcode'] ?? null,
            isActive: $data['is_active'] ?? true,
            minStockLevel: $data['min_stock_level'] ?? 0,
            maxStockLevel: $data['max_stock_level'] ?? null,
            reorderPoint: $data['reorder_point'] ?? 0,
            costMethod: $data['cost_method'] ?? 'average',
            categoryName: $data['category_name'] ?? null,
            totalStock: isset($data['total_stock']) ? (float) $data['total_stock'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'sku' => $this->sku,
            'name' => $this->name,
            'description' => $this->description,
            'category_id' => $this->categoryId,
            'unit_of_measure' => $this->unitOfMeasure,
            'barcode' => $this->barcode,
            'is_active' => $this->isActive,
            'min_stock_level' => $this->minStockLevel,
            'max_stock_level' => $this->maxStockLevel,
            'reorder_point' => $this->reorderPoint,
            'cost_method' => $this->costMethod,
            'category_name' => $this->categoryName,
            'total_stock' => $this->totalStock,
        ];
    }
}
