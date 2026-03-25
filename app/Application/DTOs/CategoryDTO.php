<?php

declare(strict_types=1);

namespace App\Application\DTOs;

readonly class CategoryDTO
{
    public function __construct(
        public ?int $id,
        public string $uuid,
        public string $name,
        public ?string $description,
        public ?int $parentId,
        public bool $isActive,
        public int $sortOrder,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            uuid: $data['uuid'],
            name: $data['name'],
            description: $data['description'] ?? null,
            parentId: $data['parent_id'] ?? null,
            isActive: $data['is_active'] ?? true,
            sortOrder: $data['sort_order'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'description' => $this->description,
            'parent_id' => $this->parentId,
            'is_active' => $this->isActive,
            'sort_order' => $this->sortOrder,
        ];
    }
}
