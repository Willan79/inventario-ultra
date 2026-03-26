<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\Supplier;
use App\Domain\Repositories\SupplierRepositoryInterface;
use App\Application\DTOs\SupplierDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class SupplierService
{
    public function __construct(
        private readonly SupplierRepositoryInterface $supplierRepository
    ) {}

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->supplierRepository->findAll($filters, $perPage);
    }

    public function getById(int $id): ?SupplierDTO
    {
        $supplier = $this->supplierRepository->findById($id);
        if (!$supplier) {
            return null;
        }

        return $this->toDTO($supplier);
    }

    public function getByUuid(string $uuid): ?SupplierDTO
    {
        $supplier = $this->supplierRepository->findByUuid($uuid);
        if (!$supplier) {
            return null;
        }

        return $this->toDTO($supplier);
    }

    public function getActive(): Collection
    {
        return $this->supplierRepository->findActive()
            ->map(fn($s) => $this->toDTO($s));
    }

    public function create(array $data): SupplierDTO
    {
        $supplier = new Supplier(
            uuid: Str::uuid()->toString(),
            name: $data['name'],
            contactName: $data['contact_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            taxId: $data['tax_id'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
            notes: $data['notes'] ?? null
        );

        $saved = $this->supplierRepository->save($supplier);
        return $this->toDTO($saved);
    }

    public function update(int $id, array $data): ?SupplierDTO
    {
        $supplier = $this->supplierRepository->findById($id);
        if (!$supplier) {
            return null;
        }

        $supplier->update(
            name: $data['name'] ?? null,
            contactName: $data['contact_name'] ?? null,
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            address: $data['address'] ?? null,
            taxId: $data['tax_id'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : null,
            notes: $data['notes'] ?? null
        );

        $saved = $this->supplierRepository->save($supplier);
        return $this->toDTO($saved);
    }

    public function delete(int $id): bool
    {
        $supplier = $this->supplierRepository->findById($id);
        if (!$supplier) {
            return false;
        }

        return $this->supplierRepository->delete($supplier);
    }

    public function toggleActive(int $id): ?SupplierDTO
    {
        $supplier = $this->supplierRepository->findById($id);
        if (!$supplier) {
            return null;
        }

        if ($supplier->isActive()) {
            $supplier->update(isActive: false);
        } else {
            $supplier->update(isActive: true);
        }

        $saved = $this->supplierRepository->save($supplier);
        return $this->toDTO($saved);
    }

    private function toDTO(Supplier $supplier): SupplierDTO
    {
        return SupplierDTO::fromArray([
            'id' => $supplier->getId(),
            'uuid' => $supplier->getUuid(),
            'name' => $supplier->getName(),
            'contact_name' => $supplier->getContactName(),
            'email' => $supplier->getEmail(),
            'phone' => $supplier->getPhone(),
            'address' => $supplier->getAddress(),
            'tax_id' => $supplier->getTaxId(),
            'is_active' => $supplier->isActive(),
            'notes' => $supplier->getNotes(),
        ]);
    }
}
