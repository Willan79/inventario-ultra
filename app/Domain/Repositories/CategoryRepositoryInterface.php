<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Domain\Entities\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public function findById(int $id): ?Category;
    public function findByUuid(string $uuid): ?Category;
    public function findByName(string $name): ?Category;
    public function findAll(array $filters = [], int $perPage = 15): \Illuminate\Pagination\LengthAwarePaginator;
    public function findActive(): Collection;
    public function findRootCategories(): Collection;
    public function findChildren(int $parentId): Collection;
    public function save(Category $category): Category;
    public function delete(Category $category): bool;
}
