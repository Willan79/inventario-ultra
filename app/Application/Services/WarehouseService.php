<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\Warehouse;
use App\Domain\Repositories\WarehouseRepositoryInterface;
use App\Application\DTOs\WarehouseDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class WarehouseService
{
    public function __construct(
        private readonly WarehouseRepositoryInterface $warehouseRepository
    ) {}

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->warehouseRepository->findAll($filters, $perPage);
    }

    public function getById(int $id): ?WarehouseDTO
    {
        $warehouse = $this->warehouseRepository->findById($id);
        return $warehouse ? $this->toDTO($warehouse) : null;
    }

    public function getByUuid(string $uuid): ?WarehouseDTO
    {
        $warehouse = $this->warehouseRepository->findByUuid($uuid);
        return $warehouse ? $this->toDTO($warehouse) : null;
    }

    public function getActive(): Collection
    {
        $warehouses = $this->warehouseRepository->findActive();
        return $warehouses->map(fn($w) => $this->toDTO($w));
    }

    public function create(array $data): WarehouseDTO
    {
        $warehouse = new Warehouse(
            uuid: Str::uuid()->toString(),
            code: $data['code'],
            name: $data['name'],
            location: $data['location'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
            managerId: $data['manager_id'] ?? null
        );

        $saved = $this->warehouseRepository->save($warehouse);
        return $this->toDTO($saved);
    }

    public function update(int $id, array $data): ?WarehouseDTO
    {
        $warehouse = $this->warehouseRepository->findById($id);
        if (!$warehouse) {
            return null;
        }

        $warehouse->update(
            name: $data['name'] ?? null,
            location: $data['location'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : null,
            managerId: $data['manager_id'] ?? null
        );

        $saved = $this->warehouseRepository->save($warehouse);
        return $this->toDTO($saved);
    }

    public function delete(int $id): bool
    {
        $warehouse = $this->warehouseRepository->findById($id);
        if (!$warehouse) {
            return false;
        }

        return $this->warehouseRepository->delete($warehouse);
    }

    public function toggleActive(int $id): ?WarehouseDTO
    {
        $warehouse = $this->warehouseRepository->findById($id);
        if (!$warehouse) {
            return null;
        }

        if ($warehouse->isActive()) {
            $warehouse->deactivate();
        } else {
            $warehouse->activate();
        }

        $saved = $this->warehouseRepository->save($warehouse);
        return $this->toDTO($saved);
    }

    private function toDTO(Warehouse $warehouse): WarehouseDTO
    {
        return WarehouseDTO::fromArray([
            'id' => $warehouse->getId(),
            'uuid' => $warehouse->getUuid(),
            'code' => $warehouse->getCode(),
            'name' => $warehouse->getName(),
            'location' => $warehouse->getLocation(),
            'is_active' => $warehouse->isActive(),
            'manager_id' => $warehouse->getManagerId(),
        ]);
    }
}
