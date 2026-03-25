<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Product;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    public function findById(int $id): ?Product;
    public function findByUuid(string $uuid): ?Product;
    public function findBySku(string $sku): ?Product;
    public function findByBarcode(string $barcode): ?Product;
    public function findAll(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    public function findActive(): Collection;
    public function findByCategory(int $categoryId): Collection;
    public function findLowStock(int $reorderPoint): Collection;
    public function search(string $term): Collection;
    public function save(Product $product): Product;
    public function delete(Product $product): bool;
    public function count(array $filters = []): int;
}
