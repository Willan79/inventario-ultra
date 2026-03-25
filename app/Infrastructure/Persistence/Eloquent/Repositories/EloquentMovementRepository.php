<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\Movement;
use App\Domain\Repositories\MovementRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\MovementModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use DateTimeInterface;

class EloquentMovementRepository implements MovementRepositoryInterface
{
    public function findById(int $id): ?Movement
    {
        $model = MovementModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByUuid(string $uuid): ?Movement
    {
        $model = MovementModel::where('uuid', $uuid)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByProduct(int $productId, int $limit = 100): Collection
    {
        return MovementModel::where('product_id', $productId)
            ->with('warehouse')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findByWarehouse(int $warehouseId, int $limit = 100): Collection
    {
        return MovementModel::where('warehouse_id', $warehouseId)
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findByDateRange(DateTimeInterface $start, DateTimeInterface $end, array $filters = []): Collection
    {
        $query = MovementModel::with(['product', 'warehouse'])
            ->whereBetween('created_at', [$start, $end]);

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        return $query->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findByReference(string $type, int $id): Collection
    {
        return MovementModel::where('reference_type', $type)
            ->where('reference_id', $id)
            ->with(['product', 'warehouse'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findAll(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        $query = MovementModel::with(['product', 'warehouse']);

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        if (!empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        if (!empty($filters['created_by'])) {
            $query->where('created_by', $filters['created_by']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', $filters['date_to']);
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($perPage);
    }

    public function count(array $filters = []): int
    {
        $query = MovementModel::query();

        if (!empty($filters['movement_type'])) {
            $query->where('movement_type', $filters['movement_type']);
        }

        if (!empty($filters['warehouse_id'])) {
            $query->where('warehouse_id', $filters['warehouse_id']);
        }

        return $query->count();
    }

    public function getMovementSummary(int $productId, int $warehouseId): array
    {
        $movements = MovementModel::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->get();

        $totalIn = $movements->whereIn('movement_type', ['in', 'return'])->sum('quantity');
        $totalOut = $movements->where('movement_type', 'out')->sum('quantity');
        $totalValue = $movements->sum('total_cost');

        return [
            'total_in' => (float) $totalIn,
            'total_out' => (float) $totalOut,
            'net_movement' => (float) ($totalIn - $totalOut),
            'total_value' => (float) $totalValue,
            'movement_count' => $movements->count(),
        ];
    }

    public function save(Movement $movement): Movement
    {
        $model = MovementModel::create([
            'uuid' => $movement->getUuid(),
            'product_id' => $movement->getProductId(),
            'warehouse_id' => $movement->getWarehouseId(),
            'movement_type' => $movement->getMovementType(),
            'reference_type' => $movement->getReferenceType(),
            'reference_id' => $movement->getReferenceId(),
            'quantity' => $movement->getQuantity(),
            'unit_cost' => $movement->getUnitCost(),
            'total_cost' => $movement->getTotalCost(),
            'previous_quantity' => $movement->getPreviousQuantity(),
            'new_quantity' => $movement->getNewQuantity(),
            'notes' => $movement->getNotes(),
            'created_by' => $movement->getCreatedBy(),
        ]);

        return $this->toEntity($model);
    }

    private function toEntity(MovementModel $model): Movement
    {
        $entity = new Movement(
            $model->uuid,
            $model->product_id,
            $model->warehouse_id,
            $model->movement_type,
            (float) $model->quantity,
            (float) $model->previous_quantity,
            (float) $model->new_quantity,
            $model->reference_type,
            $model->reference_id,
            $model->unit_cost ? (float) $model->unit_cost : null,
            $model->notes,
            $model->created_by
        );
        return $entity->setId($model->id);
    }
}
