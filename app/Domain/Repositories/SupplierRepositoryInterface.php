<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Supplier;
use Illuminate\Support\Collection;

interface SupplierRepositoryInterface
{
    public function findById(int $id): ?Supplier;
    public function findByUuid(string $uuid): ?Supplier;
    public function findByName(string $name): ?Supplier;
    public function findAll(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    public function findActive(): Collection;
    public function save(Supplier $supplier): Supplier;
    public function delete(Supplier $supplier): bool;
}
