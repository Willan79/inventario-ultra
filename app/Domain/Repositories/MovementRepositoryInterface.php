<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Movement;
use Illuminate\Support\Collection;
use DateTimeInterface;

interface MovementRepositoryInterface
{
    public function findById(int $id): ?Movement;
    public function findByUuid(string $uuid): ?Movement;
    public function findByProduct(int $productId, int $limit = 100): Collection;
    public function findByWarehouse(int $warehouseId, int $limit = 100): Collection;
    public function findByDateRange(DateTimeInterface $start, DateTimeInterface $end, array $filters = []): Collection;
    public function findByReference(string $type, int $id): Collection;
    public function findAll(array $filters = [], int $perPage = 50): \Illuminate\Pagination\LengthAwarePaginator;
    public function save(Movement $movement): Movement;
    public function count(array $filters = []): int;
    public function getMovementSummary(int $productId, int $warehouseId): array;
}
