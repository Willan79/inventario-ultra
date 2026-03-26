<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\Supplier;
use App\Domain\Repositories\SupplierRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\SupplierModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentSupplierRepository implements SupplierRepositoryInterface
{
    public function findById(int $id): ?Supplier
    {
        $model = SupplierModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByUuid(string $uuid): ?Supplier
    {
        $model = SupplierModel::where('uuid', $uuid)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByName(string $name): ?Supplier
    {
        $model = SupplierModel::where('name', $name)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = SupplierModel::query()->withCount('products');

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('contact_name', 'like', "%{$filters['search']}%")
                  ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $query->orderBy('name');

        $paginator = $query->paginate($perPage);
        
        $items = collect($paginator->items())->map(function ($model) {
            return \App\Application\DTOs\SupplierDTO::fromArray([
                'id' => $model->id,
                'uuid' => $model->uuid,
                'name' => $model->name,
                'contact_name' => $model->contact_name,
                'email' => $model->email,
                'phone' => $model->phone,
                'address' => $model->address,
                'tax_id' => $model->tax_id,
                'is_active' => $model->is_active,
                'notes' => $model->notes,
                'products_count' => $model->products_count ?? 0,
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
        return SupplierModel::active()
            ->orderBy('name')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function save(Supplier $supplier): Supplier
    {
        $model = SupplierModel::updateOrCreate(
            ['id' => $supplier->getId()],
            [
                'uuid' => $supplier->getUuid(),
                'name' => $supplier->getName(),
                'contact_name' => $supplier->getContactName(),
                'email' => $supplier->getEmail(),
                'phone' => $supplier->getPhone(),
                'address' => $supplier->getAddress(),
                'tax_id' => $supplier->getTaxId(),
                'is_active' => $supplier->isActive(),
                'notes' => $supplier->getNotes(),
            ]
        );

        return $this->toEntity($model);
    }

    public function delete(Supplier $supplier): bool
    {
        $model = SupplierModel::find($supplier->getId());
        return $model ? $model->delete() : false;
    }

    private function toEntity(SupplierModel $model): Supplier
    {
        $entity = new Supplier(
            $model->uuid,
            $model->name,
            $model->contact_name,
            $model->email,
            $model->phone,
            $model->address,
            $model->tax_id,
            $model->is_active,
            $model->notes
        );
        return $entity->setId($model->id);
    }
}
