<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\Warehouse;
use App\Domain\Repositories\WarehouseRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\WarehouseModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentWarehouseRepository implements WarehouseRepositoryInterface
{
    public function findById(int $id): ?Warehouse
    {
        $model = WarehouseModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByUuid(string $uuid): ?Warehouse
    {
        $model = WarehouseModel::where('uuid', $uuid)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByCode(string $code): ?Warehouse
    {
        $model = WarehouseModel::where('code', strtoupper($code))->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = WarehouseModel::query();

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('code', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $query->orderBy('name');

        $paginator = $query->paginate($perPage);
        
        $items = collect($paginator->items())->map(function ($model) {
            return \App\Application\DTOs\WarehouseDTO::fromArray([
                'id' => $model->id,
                'uuid' => $model->uuid,
                'code' => $model->code,
                'name' => $model->name,
                'location' => $model->location,
                'is_active' => $model->is_active,
                'manager_id' => $model->manager_id,
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

    public function findActive(): Collection
    {
        return WarehouseModel::active()
            ->orderBy('name')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function save(Warehouse $warehouse): Warehouse
    {
        $model = WarehouseModel::updateOrCreate(
            ['id' => $warehouse->getId()],
            [
                'uuid' => $warehouse->getUuid(),
                'code' => $warehouse->getCode(),
                'name' => $warehouse->getName(),
                'location' => $warehouse->getLocation(),
                'is_active' => $warehouse->isActive(),
                'manager_id' => $warehouse->getManagerId(),
            ]
        );

        return $this->toEntity($model);
    }

    public function delete(Warehouse $warehouse): bool
    {
        $model = WarehouseModel::find($warehouse->getId());
        return $model ? $model->delete() : false;
    }

    public function count(array $filters = []): int
    {
        $query = WarehouseModel::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->count();
    }

    private function toEntity(WarehouseModel $model): Warehouse
    {
        $entity = new Warehouse(
            $model->uuid,
            $model->code,
            $model->name,
            $model->location,
            $model->is_active,
            $model->manager_id
        );
        return $entity->setId($model->id);
    }
}
