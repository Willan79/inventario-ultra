<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\PurchaseOrderItem;
use Illuminate\Support\Collection;

interface PurchaseOrderItemRepositoryInterface
{
    public function findById(int $id): ?PurchaseOrderItem;
    public function findByOrder(int $orderId): Collection;
    public function findByProduct(int $productId): Collection;
    public function save(PurchaseOrderItem $item): PurchaseOrderItem;
    public function delete(PurchaseOrderItem $item): bool;
    public function deleteByOrder(int $orderId): bool;
}
