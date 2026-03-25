<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\Inventory;
use App\Domain\Repositories\InventoryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\InventoryModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EloquentInventoryRepository implements InventoryRepositoryInterface
{
    public function findById(int $id): ?Inventory
    {
        $model = InventoryModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByProductAndWarehouse(int $productId, int $warehouseId): ?Inventory
    {
        $model = InventoryModel::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByProduct(int $productId): Collection
    {
        return InventoryModel::where('product_id', $productId)
            ->with('warehouse')
            ->get()
            ->map(function ($model) {
                return \App\Application\DTOs\InventoryDTO::fromArray([
                    'id' => $model->id,
                    'product_id' => $model->product_id,
                    'warehouse_id' => $model->warehouse_id,
                    'quantity_available' => $model->quantity_available,
                    'quantity_reserved' => $model->quantity_reserved,
                    'quantity_on_order' => $model->quantity_on_order,
                    'average_cost' => $model->average_cost,
                    'warehouse_name' => $model->warehouse?->name,
                    'warehouse_code' => $model->warehouse?->code,
                ]);
            });
    }

    public function findByWarehouse(int $warehouseId): Collection
    {
        return InventoryModel::where('warehouse_id', $warehouseId)
            ->with('product')
            ->get()
            ->map(function ($model) {
                return \App\Application\DTOs\InventoryDTO::fromArray([
                    'id' => $model->id,
                    'product_id' => $model->product_id,
                    'warehouse_id' => $model->warehouse_id,
                    'quantity_available' => $model->quantity_available,
                    'quantity_reserved' => $model->quantity_reserved,
                    'quantity_on_order' => $model->quantity_on_order,
                    'average_cost' => $model->average_cost,
                    'product_name' => $model->product?->name,
                    'product_sku' => $model->product?->sku,
                ]);
            });
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = InventoryModel::with(['product', 'warehouse']);

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['low_stock'])) {
            $query->lowStock($filters['low_stock']);
        }

        $query->orderBy('updated_at', 'desc');

        $paginator = $query->paginate($perPage);
        
        $items = collect($paginator->items())->map(function ($model) {
            return \App\Application\DTOs\InventoryDTO::fromArray([
                'id' => $model->id,
                'product_id' => $model->product_id,
                'warehouse_id' => $model->warehouse_id,
                'quantity_available' => $model->quantity_available,
                'quantity_reserved' => $model->quantity_reserved,
                'quantity_on_order' => $model->quantity_on_order,
                'average_cost' => $model->average_cost,
                'product_name' => $model->product?->name,
                'product_sku' => $model->product?->sku,
                'warehouse_name' => $model->warehouse?->name,
                'warehouse_code' => $model->warehouse?->code,
            ]);
        })->toArray();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $paginator->total(),
            $paginator->perPage(),
            $paginator->currentPage(),
            ['path' => $paginator->path()]
        );
    }

    public function findLowStock(int $threshold = 0): Collection
    {
        return InventoryModel::with(['product', 'warehouse'])
            ->lowStock($threshold)
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findBelowReorderPoint(): Collection
    {
        return InventoryModel::with(['product', 'warehouse'])
            ->select('inventories.*')
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->whereRaw('(inventories.quantity_available - inventories.quantity_reserved) <= products.min_stock_level')
            ->where('products.is_active', true)
            ->where('products.min_stock_level', '>', 0)
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function save(Inventory $inventory): Inventory
    {
        $model = InventoryModel::where('product_id', $inventory->getProductId())
            ->where('warehouse_id', $inventory->getWarehouseId())
            ->first();

        if ($model) {
            $model->update([
                'quantity_available' => $inventory->getQuantityAvailable(),
                'quantity_reserved' => $inventory->getQuantityReserved(),
                'quantity_on_order' => $inventory->getQuantityOnOrder(),
                'average_cost' => $inventory->getAverageCost(),
                'last_movement_at' => $inventory->getLastMovementAt(),
            ]);
        } else {
            $model = InventoryModel::create([
                'product_id' => $inventory->getProductId(),
                'warehouse_id' => $inventory->getWarehouseId(),
                'quantity_available' => $inventory->getQuantityAvailable(),
                'quantity_reserved' => $inventory->getQuantityReserved(),
                'quantity_on_order' => $inventory->getQuantityOnOrder(),
                'average_cost' => $inventory->getAverageCost(),
                'last_movement_at' => $inventory->getLastMovementAt(),
            ]);
        }

        return $this->toEntity($model);
    }

    public function delete(Inventory $inventory): bool
    {
        $model = InventoryModel::find($inventory->getId());
        return $model ? $model->delete() : false;
    }

    public function getTotalValue(int $warehouseId = null): float
    {
        $query = InventoryModel::query();

        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        return (float) $query->sum(DB::raw('quantity_available * average_cost'));
    }

    public function getTotalQuantity(int $warehouseId = null): float
    {
        $query = InventoryModel::query();

        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        return (float) $query->sum('quantity_available');
    }

    private function toEntity(InventoryModel $model): Inventory
    {
        $entity = new Inventory(
            $model->product_id,
            $model->warehouse_id,
            (float) $model->quantity_available,
            (float) $model->quantity_reserved,
            (float) $model->quantity_on_order,
            (float) $model->average_cost
        );
        return $entity->setId($model->id);
    }
}
