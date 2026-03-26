<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\PurchaseOrderItem;
use App\Domain\Repositories\PurchaseOrderItemRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\PurchaseOrderItemModel;
use Illuminate\Support\Collection;

class EloquentPurchaseOrderItemRepository implements PurchaseOrderItemRepositoryInterface
{
    public function findById(int $id): ?PurchaseOrderItem
    {
        $model = PurchaseOrderItemModel::with('product')->find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByOrder(int $orderId): Collection
    {
        return PurchaseOrderItemModel::with('product')
            ->where('purchase_order_id', $orderId)
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findByProduct(int $productId): Collection
    {
        return PurchaseOrderItemModel::where('product_id', $productId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function save(PurchaseOrderItem $item): PurchaseOrderItem
    {
        $model = PurchaseOrderItemModel::updateOrCreate(
            ['id' => $item->getId()],
            [
                'purchase_order_id' => $item->getPurchaseOrderId(),
                'product_id' => $item->getProductId(),
                'quantity' => $item->getQuantity(),
                'quantity_received' => $item->getQuantityReceived(),
                'unit_cost' => $item->getUnitCost(),
                'total' => $item->getTotal(),
                'supplier_sku' => $item->getSupplierSku(),
                'notes' => $item->getNotes(),
            ]
        );

        return $this->toEntity($model);
    }

    public function delete(PurchaseOrderItem $item): bool
    {
        $model = PurchaseOrderItemModel::find($item->getId());
        return $model ? $model->delete() : false;
    }

    public function deleteByOrder(int $orderId): bool
    {
        return PurchaseOrderItemModel::where('purchase_order_id', $orderId)->delete();
    }

    private function toEntity(PurchaseOrderItemModel $model): PurchaseOrderItem
    {
        $entity = new PurchaseOrderItem(
            purchaseOrderId: $model->purchase_order_id,
            productId: $model->product_id,
            quantity: $model->quantity,
            unitCost: (float) $model->unit_cost,
            supplierSku: $model->supplier_sku,
            notes: $model->notes
        );
        $entity->setId($model->id);
        if ($model->quantity_received > 0) {
            $entity->addReceived(0);
        }
        return $entity;
    }
}
