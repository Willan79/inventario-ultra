<?php

declare(strict_types=1);

namespace App\Application\DTOs;

readonly class WarehouseDTO
{
    public function __construct(
        public ?int $id,
        public string $uuid,
        public string $code,
        public string $name,
        public ?string $location,
        public bool $isActive,
        public ?int $managerId,
        public ?int $productCount = null,
        public ?float $totalValue = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            uuid: $data['uuid'],
            code: $data['code'],
            name: $data['name'],
            location: $data['location'] ?? null,
            isActive: $data['is_active'] ?? true,
            managerId: $data['manager_id'] ?? null,
            productCount: isset($data['product_count']) ? (int) $data['product_count'] : null,
            totalValue: isset($data['total_value']) ? (float) $data['total_value'] : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'code' => $this->code,
            'name' => $this->name,
            'location' => $this->location,
            'is_active' => $this->isActive,
            'manager_id' => $this->managerId,
            'product_count' => $this->productCount,
            'total_value' => $this->totalValue,
        ];
    }
}
