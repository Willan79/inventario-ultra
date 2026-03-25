<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\Category;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentCategoryRepository implements CategoryRepositoryInterface
{
    public function findById(int $id): ?Category
    {
        $model = CategoryModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByUuid(string $uuid): ?Category
    {
        $model = CategoryModel::where('uuid', $uuid)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByName(string $name): ?Category
    {
        $model = CategoryModel::where('name', $name)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = CategoryModel::query()->with('parent');

        if (!empty($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%");
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $query->orderBy('sort_order')->orderBy('name');

        $paginator = $query->paginate($perPage);
        
        $items = collect($paginator->items())->map(function ($model) {
            return \App\Application\DTOs\CategoryDTO::fromArray([
                'id' => $model->id,
                'uuid' => $model->uuid,
                'name' => $model->name,
                'description' => $model->description,
                'parent_id' => $model->parent_id,
                'is_active' => $model->is_active,
                'sort_order' => $model->sort_order,
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
        return CategoryModel::active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findRootCategories(): Collection
    {
        return CategoryModel::root()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findChildren(int $parentId): Collection
    {
        return CategoryModel::where('parent_id', $parentId)
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function save(Category $category): Category
    {
        $model = CategoryModel::updateOrCreate(
            ['id' => $category->getId()],
            [
                'uuid' => $category->getUuid(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'parent_id' => $category->getParentId(),
                'is_active' => $category->isActive(),
                'sort_order' => $category->getSortOrder(),
            ]
        );

        return $this->toEntity($model);
    }

    public function delete(Category $category): bool
    {
        $model = CategoryModel::find($category->getId());
        return $model ? $model->delete() : false;
    }

    private function toEntity(CategoryModel $model): Category
    {
        $entity = new Category(
            $model->uuid,
            $model->name,
            $model->description,
            $model->parent_id,
            $model->is_active,
            $model->sort_order
        );
        return $entity->setId($model->id);
    }
}
