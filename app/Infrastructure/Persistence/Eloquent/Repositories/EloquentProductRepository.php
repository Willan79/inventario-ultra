<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\Product;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Domain\ValueObjects\SKU;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ?Product
    {
        $model = ProductModel::find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByUuid(string $uuid): ?Product
    {
        $model = ProductModel::where('uuid', $uuid)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findBySku(string $sku): ?Product
    {
        $model = ProductModel::where('sku', $sku)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByBarcode(string $barcode): ?Product
    {
        $model = ProductModel::where('barcode', $barcode)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = ProductModel::query()->with('category');

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['sort_by'])) {
            $direction = $filters['sort_direction'] ?? 'asc';
            $query->orderBy($filters['sort_by'], $direction);
        } else {
            $query->orderBy('name');
        }

        $paginator = $query->paginate($perPage);
        
        $items = collect($paginator->items())->map(function ($model) {
            return \App\Application\DTOs\ProductDTO::fromArray([
                'id' => $model->id,
                'uuid' => $model->uuid,
                'sku' => $model->sku,
                'name' => $model->name,
                'description' => $model->description,
                'category_id' => $model->category_id,
                'unit_of_measure' => $model->unit_of_measure,
                'barcode' => $model->barcode,
                'is_active' => $model->is_active,
                'min_stock_level' => $model->min_stock_level,
                'max_stock_level' => $model->max_stock_level,
                'reorder_point' => $model->reorder_point,
                'cost_method' => $model->cost_method,
                'category_name' => $model->category?->name,
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
        return ProductModel::active()
            ->orderBy('name')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findByCategory(int $categoryId): Collection
    {
        return ProductModel::where('category_id', $categoryId)
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findLowStock(int $reorderPoint): Collection
    {
        return ProductModel::active()
            ->where('reorder_point', '>', 0)
            ->whereHas('inventories', function($q) use ($reorderPoint) {
                $q->selectRaw('product_id, SUM(quantity_available - quantity_reserved) as available')
                  ->groupBy('product_id')
                  ->having('available', '<=', $reorderPoint);
            })
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function search(string $term): Collection
    {
        return ProductModel::search($term)
            ->active()
            ->limit(50)
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function save(Product $product): Product
    {
        $model = ProductModel::updateOrCreate(
            ['id' => $product->getId()],
            [
                'uuid' => $product->getUuid(),
                'sku' => $product->getSku()->getValue(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'category_id' => $product->getCategoryId(),
                'unit_of_measure' => $product->getUnitOfMeasure(),
                'barcode' => $product->getBarcode(),
                'is_active' => $product->isActive(),
                'min_stock_level' => $product->getMinStockLevel(),
                'max_stock_level' => $product->getMaxStockLevel(),
                'reorder_point' => $product->getReorderPoint(),
                'cost_method' => $product->getCostMethod(),
            ]
        );

        return $this->toEntity($model);
    }

    public function delete(Product $product): bool
    {
        $model = ProductModel::find($product->getId());
        return $model ? $model->delete() : false;
    }

    public function count(array $filters = []): int
    {
        $query = ProductModel::query();

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->count();
    }

    private function toEntity(ProductModel $model): Product
    {
        $entity = new Product(
            $model->uuid,
            new SKU($model->sku),
            $model->name,
            $model->description,
            $model->category_id,
            $model->unit_of_measure,
            $model->barcode,
            $model->is_active,
            $model->min_stock_level,
            $model->max_stock_level,
            $model->reorder_point,
            $model->cost_method
        );
        return $entity->setId($model->id);
    }
}
