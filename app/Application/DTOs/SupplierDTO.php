<?php

declare(strict_types=1);

namespace App\Application\DTOs;

readonly class SupplierDTO
{
    public function __construct(
        public ?int $id,
        public string $uuid,
        public string $name,
        public ?string $contactName,
        public ?string $email,
        public ?string $phone,
        public ?string $address,
        public ?string $taxId,
        public bool $isActive,
        public ?string $notes,
        public int $productsCount = 0,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            uuid: $data['uuid'],
            name: $data['name'],
            contactName: $data['contact_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            taxId: $data['tax_id'] ?? null,
            isActive: $data['is_active'] ?? true,
            notes: $data['notes'] ?? null,
            productsCount: $data['products_count'] ?? 0,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'contact_name' => $this->contactName,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'tax_id' => $this->taxId,
            'is_active' => $this->isActive,
            'notes' => $this->notes,
            'products_count' => $this->productsCount,
        ];
    }
}
