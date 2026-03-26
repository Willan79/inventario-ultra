<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\PurchaseOrder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface PurchaseOrderRepositoryInterface
{
    public function findById(int $id): ?PurchaseOrder;
    public function findByUuid(string $uuid): ?PurchaseOrder;
    public function findByOrderNumber(string $orderNumber): ?PurchaseOrder;
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
    public function findBySupplier(int $supplierId): Collection;
    public function findPending(): Collection;
    public function save(PurchaseOrder $order): PurchaseOrder;
    public function delete(PurchaseOrder $order): bool;
}
