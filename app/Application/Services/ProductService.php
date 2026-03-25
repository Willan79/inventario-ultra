<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\Product;
use App\Domain\ValueObjects\SKU;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Domain\Repositories\InventoryRepositoryInterface;
use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Application\DTOs\ProductDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class ProductService
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly InventoryRepositoryInterface $inventoryRepository,
        private readonly CategoryRepositoryInterface $categoryRepository
    ) {}

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->productRepository->findAll($filters, $perPage);
    }

    public function getById(int $id): ?ProductDTO
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            return null;
        }

        $inventories = $this->inventoryRepository->findByProduct($id);
        $totalStock = $inventories->sum(fn($inv) => $inv->quantityAvailable);

        return $this->toDTO($product, $totalStock);
    }

    public function getByUuid(string $uuid): ?ProductDTO
    {
        $product = $this->productRepository->findByUuid($uuid);
        if (!$product) {
            return null;
        }

        return $this->toDTO($product);
    }

    public function search(string $term): Collection
    {
        $products = $this->productRepository->search($term);
        return $products->map(fn($p) => $this->toDTO($p));
    }

    public function getLowStock(): Collection
    {
        $products = $this->productRepository->findLowStock(0);
        return $products->map(fn($p) => $this->toDTO($p));
    }

    public function create(array $data): ProductDTO
    {
        $categoryId = null;
        if (!empty($data['category_id']) && is_numeric($data['category_id'])) {
            $categoryId = (int) $data['category_id'];
        }

        $sku = !empty($data['sku']) ? $data['sku'] : $this->generateSKU($data['name']);

        $product = new Product(
            uuid: Str::uuid()->toString(),
            sku: new SKU($sku),
            name: $data['name'],
            description: $data['description'] ?? null,
            categoryId: $categoryId,
            unitOfMeasure: $data['unit_of_measure'] ?? 'unit',
            barcode: $data['barcode'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
            minStockLevel: (int) ($data['min_stock_level'] ?? 0),
            maxStockLevel: isset($data['max_stock_level']) && is_numeric($data['max_stock_level']) ? (int) $data['max_stock_level'] : null,
            reorderPoint: (int) ($data['reorder_point'] ?? 0),
            costMethod: $data['cost_method'] ?? 'average'
        );

        $saved = $this->productRepository->save($product);
        return $this->toDTO($saved);
    }

    public function update(int $id, array $data): ?ProductDTO
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            return null;
        }

        $categoryId = null;
        if (isset($data['category_id'])) {
            $categoryId = !empty($data['category_id']) && is_numeric($data['category_id']) 
                ? (int) $data['category_id'] 
                : null;
        }

        $product->update(
            name: $data['name'] ?? null,
            description: $data['description'] ?? null,
            categoryId: $categoryId,
            unitOfMeasure: $data['unit_of_measure'] ?? null,
            barcode: $data['barcode'] ?? null,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : null,
            minStockLevel: isset($data['min_stock_level']) && is_numeric($data['min_stock_level']) ? (int) $data['min_stock_level'] : null,
            maxStockLevel: isset($data['max_stock_level']) && is_numeric($data['max_stock_level']) ? (int) $data['max_stock_level'] : null,
            reorderPoint: isset($data['reorder_point']) && is_numeric($data['reorder_point']) ? (int) $data['reorder_point'] : null
        );

        if (isset($data['sku'])) {
            $newProduct = new Product(
                $product->getUuid(),
                new SKU($data['sku']),
                $product->getName(),
                $product->getDescription(),
                $product->getCategoryId(),
                $product->getUnitOfMeasure(),
                $product->getBarcode(),
                $product->isActive(),
                $product->getMinStockLevel(),
                $product->getMaxStockLevel(),
                $product->getReorderPoint(),
                $product->getCostMethod()
            );
            $newProduct->setId($id);
            $product = $newProduct;
        }

        $saved = $this->productRepository->save($product);
        return $this->toDTO($saved);
    }

    public function delete(int $id): bool
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            return false;
        }

        $product->softDelete();
        return $this->productRepository->delete($product);
    }

    public function toggleActive(int $id): ?ProductDTO
    {
        $product = $this->productRepository->findById($id);
        if (!$product) {
            return null;
        }

        if ($product->isActive()) {
            $product->deactivate();
        } else {
            $product->activate();
        }

        $saved = $this->productRepository->save($product);
        return $this->toDTO($saved);
    }

    private function toDTO(Product $product, ?float $totalStock = null): ProductDTO
    {
        $categoryName = null;
        if ($product->getCategoryId()) {
            $category = $this->categoryRepository->findById($product->getCategoryId());
            $categoryName = $category?->getName();
        }

        return ProductDTO::fromArray([
            'id' => $product->getId(),
            'uuid' => $product->getUuid(),
            'sku' => $product->getSku()->getValue(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'category_id' => $product->getCategoryId(),
            'category_name' => $categoryName,
            'unit_of_measure' => $product->getUnitOfMeasure(),
            'barcode' => $product->getBarcode(),
            'is_active' => $product->isActive(),
            'min_stock_level' => $product->getMinStockLevel(),
            'max_stock_level' => $product->getMaxStockLevel(),
            'reorder_point' => $product->getReorderPoint(),
            'cost_method' => $product->getCostMethod(),
            'total_stock' => $totalStock,
        ]);
    }

    private function generateSKU(string $name): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3));
        $prefix = str_pad($prefix, 3, 'X');
        
        $count = $this->productRepository->count() + 1;
        
        return $prefix . '-' . str_pad((string) $count, 6, '0', STR_PAD_LEFT);
    }
}
