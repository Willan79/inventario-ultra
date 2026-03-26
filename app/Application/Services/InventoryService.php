<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\Inventory;
use App\Domain\Entities\Movement;
use App\Domain\Exceptions\InsufficientStockException;
use App\Domain\Repositories\InventoryRepositoryInterface;
use App\Domain\Repositories\MovementRepositoryInterface;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Domain\Repositories\WarehouseRepositoryInterface;
use App\Application\DTOs\InventoryDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class InventoryService
{
    public function __construct(
        private readonly InventoryRepositoryInterface $inventoryRepository,
        private readonly MovementRepositoryInterface $movementRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly WarehouseRepositoryInterface $warehouseRepository
    ) {}

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->inventoryRepository->findAll($filters, $perPage);
    }

    public function getByProduct(int $productId): Collection
    {
        $inventories = $this->inventoryRepository->findByProduct($productId);
        return $inventories->map(fn($i) => $this->toDTO($i));
    }

    public function getByWarehouse(int $warehouseId): Collection
    {
        $inventories = $this->inventoryRepository->findByWarehouse($warehouseId);
        return $inventories->map(fn($i) => $this->toDTO($i));
    }

    public function getLowStock(): Collection
    {
        $inventories = $this->inventoryRepository->findBelowReorderPoint();
        return $inventories->map(fn($i) => $this->toDTO($i));
    }

    public function addStock(
        int $productId,
        int $warehouseId,
        float $quantity,
        ?float $unitCost = null,
        ?string $notes = null,
        ?int $createdBy = null,
        ?int $supplierId = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): InventoryDTO {
        $inventory = $this->getOrCreateInventory($productId, $warehouseId);
        
        $previousQty = $inventory->getQuantityAvailable();
        $inventory->addStock($quantity, $unitCost);
        
        $this->inventoryRepository->save($inventory);
        $this->createMovement(
            $productId,
            $warehouseId,
            Movement::TYPE_IN,
            $quantity,
            $previousQty,
            $inventory->getQuantityAvailable(),
            $unitCost,
            $notes,
            $createdBy,
            $supplierId,
            $referenceType,
            $referenceId
        );

        return $this->toDTO($inventory);
    }

    public function removeStock(
        int $productId,
        int $warehouseId,
        float $quantity,
        ?string $notes = null,
        ?int $createdBy = null
    ): InventoryDTO {
        $inventory = $this->getOrCreateInventory($productId, $warehouseId);
        
        $previousQty = $inventory->getQuantityAvailable();
        
        try {
            $inventory->removeStock($quantity);
        } catch (InsufficientStockException $e) {
            throw $e;
        }
        
        $this->inventoryRepository->save($inventory);
        $this->createMovement(
            $productId,
            $warehouseId,
            Movement::TYPE_OUT,
            $quantity,
            $previousQty,
            $inventory->getQuantityAvailable(),
            null,
            $notes,
            $createdBy
        );

        return $this->toDTO($inventory);
    }

    public function adjustStock(
        int $productId,
        int $warehouseId,
        float $newQuantity,
        string $reason,
        ?int $createdBy = null
    ): InventoryDTO {
        $inventory = $this->getOrCreateInventory($productId, $warehouseId);
        
        $previousQty = $inventory->getQuantityAvailable();
        $inventory->adjustStock($newQuantity, $reason);
        
        $this->inventoryRepository->save($inventory);
        $this->createMovement(
            $productId,
            $warehouseId,
            Movement::TYPE_ADJUSTMENT,
            abs($newQuantity - $previousQty),
            $previousQty,
            $newQuantity,
            null,
            $reason,
            $createdBy
        );

        return $this->toDTO($inventory);
    }

    public function reserve(
        int $productId,
        int $warehouseId,
        float $quantity
    ): InventoryDTO {
        $inventory = $this->getOrCreateInventory($productId, $warehouseId);
        $inventory->reserve($quantity);
        
        $this->inventoryRepository->save($inventory);
        return $this->toDTO($inventory);
    }

    public function releaseReservation(
        int $productId,
        int $warehouseId,
        float $quantity
    ): InventoryDTO {
        $inventory = $this->getOrCreateInventory($productId, $warehouseId);
        $inventory->releaseReservation($quantity);
        
        $this->inventoryRepository->save($inventory);
        return $this->toDTO($inventory);
    }

    public function transfer(
        int $productId,
        int $fromWarehouseId,
        int $toWarehouseId,
        float $quantity,
        ?int $createdBy = null
    ): array {
        $fromInventory = $this->getOrCreateInventory($productId, $fromWarehouseId);
        $toInventory = $this->getOrCreateInventory($productId, $toWarehouseId);

        $previousFrom = $fromInventory->getQuantityAvailable();
        $previousTo = $toInventory->getQuantityAvailable();

        $fromInventory->removeStock($quantity);
        $toInventory->addStock($quantity);

        $this->inventoryRepository->save($fromInventory);
        $this->inventoryRepository->save($toInventory);

        $this->createMovement(
            $productId,
            $fromWarehouseId,
            Movement::TYPE_TRANSFER,
            $quantity,
            $previousFrom,
            $fromInventory->getQuantityAvailable(),
            null,
            "Transferencia al almacén {$toWarehouseId}",
            $createdBy
        );

        $this->createMovement(
            $productId,
            $toWarehouseId,
            Movement::TYPE_TRANSFER,
            $quantity,
            $previousTo,
            $toInventory->getQuantityAvailable(),
            null,
            "Transferencia desde almacén {$fromWarehouseId}",
            $createdBy
        );

        return [
            'from' => $this->toDTO($fromInventory),
            'to' => $this->toDTO($toInventory),
        ];
    }

    public function getTotalValue(?int $warehouseId = null): float
    {
        return $this->inventoryRepository->getTotalValue($warehouseId);
    }

    public function getTotalQuantity(?int $warehouseId = null): float
    {
        return $this->inventoryRepository->getTotalQuantity($warehouseId);
    }

    private function getOrCreateInventory(int $productId, int $warehouseId): Inventory
    {
        $inventory = $this->inventoryRepository->findByProductAndWarehouse($productId, $warehouseId);

        if (!$inventory) {
            $inventory = new Inventory($productId, $warehouseId);
        }

        return $inventory;
    }

    private function createMovement(
        int $productId,
        int $warehouseId,
        string $type,
        float $quantity,
        float $previousQty,
        float $newQty,
        ?float $unitCost = null,
        ?string $notes = null,
        ?int $createdBy = null,
        ?int $supplierId = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): Movement {
        $movement = new Movement(
            uuid: Str::uuid()->toString(),
            productId: $productId,
            warehouseId: $warehouseId,
            movementType: $type,
            quantity: $quantity,
            previousQuantity: $previousQty,
            newQuantity: $newQty,
            unitCost: $unitCost,
            notes: $notes,
            createdBy: $createdBy,
            supplierId: $supplierId,
            referenceType: $referenceType,
            referenceId: $referenceId
        );

        return $this->movementRepository->save($movement);
    }

    private function toDTO(Inventory $inventory): InventoryDTO
    {
        $product = $this->productRepository->findById($inventory->getProductId());
        $warehouse = $this->warehouseRepository->findById($inventory->getWarehouseId());

        return InventoryDTO::fromArray([
            'id' => $inventory->getId(),
            'product_id' => $inventory->getProductId(),
            'warehouse_id' => $inventory->getWarehouseId(),
            'quantity_available' => $inventory->getQuantityAvailable(),
            'quantity_reserved' => $inventory->getQuantityReserved(),
            'quantity_on_order' => $inventory->getQuantityOnOrder(),
            'average_cost' => $inventory->getAverageCost(),
            'product_name' => $product?->getName(),
            'product_sku' => $product?->getSku()->getValue(),
            'warehouse_name' => $warehouse?->getName(),
            'warehouse_code' => $warehouse?->getCode(),
        ]);
    }
}
