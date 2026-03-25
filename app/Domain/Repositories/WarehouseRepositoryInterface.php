<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Warehouse;
use Illuminate\Support\Collection;

interface WarehouseRepositoryInterface
{
    public function findById(int $id): ?Warehouse;
    public function findByUuid(string $uuid): ?Warehouse;
    public function findByCode(string $code): ?Warehouse;
    public function findAll(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    public function findActive(): Collection;
    public function save(Warehouse $warehouse): Warehouse;
    public function delete(Warehouse $warehouse): bool;
    public function count(array $filters = []): int;
}
