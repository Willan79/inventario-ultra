<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Repositories\CategoryRepositoryInterface;
use App\Domain\Repositories\InventoryRepositoryInterface;
use App\Domain\Repositories\MovementRepositoryInterface;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Domain\Repositories\PurchaseOrderItemRepositoryInterface;
use App\Domain\Repositories\PurchaseOrderRepositoryInterface;
use App\Domain\Repositories\SupplierRepositoryInterface;
use App\Domain\Repositories\WarehouseRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentCategoryRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentInventoryRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentMovementRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentProductRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentPurchaseOrderItemRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentPurchaseOrderRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentSupplierRepository;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentWarehouseRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
        $this->app->bind(WarehouseRepositoryInterface::class, EloquentWarehouseRepository::class);
        $this->app->bind(InventoryRepositoryInterface::class, EloquentInventoryRepository::class);
        $this->app->bind(MovementRepositoryInterface::class, EloquentMovementRepository::class);
        $this->app->bind(CategoryRepositoryInterface::class, EloquentCategoryRepository::class);
        $this->app->bind(SupplierRepositoryInterface::class, EloquentSupplierRepository::class);
        $this->app->bind(PurchaseOrderRepositoryInterface::class, EloquentPurchaseOrderRepository::class);
        $this->app->bind(PurchaseOrderItemRepositoryInterface::class, EloquentPurchaseOrderItemRepository::class);
    }

    public function boot(): void
    {
    }
}
