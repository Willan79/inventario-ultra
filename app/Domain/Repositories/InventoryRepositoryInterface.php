<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Inventory;
use Illuminate\Support\Collection;

interface InventoryRepositoryInterface
{
    public function findById(int $id): ?Inventory;
    public function findByProductAndWarehouse(int $productId, int $warehouseId): ?Inventory;
    public function findByProduct(int $productId): Collection;
    public function findByWarehouse(int $warehouseId): Collection;
    public function findAll(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    public function findLowStock(int $threshold = 0): Collection;
    public function findBelowReorderPoint(): Collection;
    public function save(Inventory $inventory): Inventory;
    public function delete(Inventory $inventory): bool;
    public function getTotalValue(int $warehouseId = null): float;
    public function getTotalQuantity(int $warehouseId = null): float;
}
