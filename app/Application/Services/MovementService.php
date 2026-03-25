<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\Movement;
use App\Domain\Repositories\MovementRepositoryInterface;
use App\Application\DTOs\MovementDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use DateTimeInterface;

class MovementService
{
    public function __construct(
        private readonly MovementRepositoryInterface $movementRepository
    ) {}

    public function create(array $data): MovementDTO
    {
        $movement = new Movement(
            uuid: $data['uuid'],
            productId: (int) $data['product_id'],
            warehouseId: (int) $data['warehouse_id'],
            movementType: $data['movement_type'],
            quantity: (float) $data['quantity'],
            previousQuantity: (float) $data['previous_quantity'],
            newQuantity: (float) $data['new_quantity'],
            referenceType: $data['reference_type'] ?? null,
            referenceId: isset($data['reference_id']) ? (int) $data['reference_id'] : null,
            unitCost: isset($data['unit_cost']) ? (float) $data['unit_cost'] : null,
            notes: $data['notes'] ?? null,
            createdBy: $data['created_by'] ?? null
        );

        $saved = $this->movementRepository->save($movement);
        return $this->toDTO($saved);
    }

    public function getAll(array $filters = [], int $perPage = 50): LengthAwarePaginator
    {
        return $this->movementRepository->findAll($filters, $perPage);
    }

    public function getById(int $id): ?MovementDTO
    {
        $movement = $this->movementRepository->findById($id);
        return $movement ? $this->toDTO($movement) : null;
    }

    public function getByProduct(int $productId, int $limit = 100): Collection
    {
        $movements = $this->movementRepository->findByProduct($productId, $limit);
        return $movements->map(fn($m) => $this->toDTO($m));
    }

    public function getByWarehouse(int $warehouseId, int $limit = 100): Collection
    {
        $movements = $this->movementRepository->findByWarehouse($warehouseId, $limit);
        return $movements->map(fn($m) => $this->toDTO($m));
    }

    public function getByDateRange(
        DateTimeInterface $start,
        DateTimeInterface $end,
        array $filters = []
    ): Collection {
        $movements = $this->movementRepository->findByDateRange($start, $end, $filters);
        return $movements->map(fn($m) => $this->toDTO($m));
    }

    public function getSummary(int $productId, int $warehouseId): array
    {
        return $this->movementRepository->getMovementSummary($productId, $warehouseId);
    }

    public function getInboundSummary(int $warehouseId = null): array
    {
        $filters = ['movement_type' => Movement::TYPE_IN];
        if ($warehouseId) {
            $filters['warehouse_id'] = $warehouseId;
        }

        return [
            'total_quantity' => $this->movementRepository->count($filters),
        ];
    }

    public function getOutboundSummary(int $warehouseId = null): array
    {
        $filters = ['movement_type' => Movement::TYPE_OUT];
        if ($warehouseId) {
            $filters['warehouse_id'] = $warehouseId;
        }

        return [
            'total_quantity' => $this->movementRepository->count($filters),
        ];
    }

    private function toDTO(Movement $movement): MovementDTO
    {
        return MovementDTO::fromArray([
            'id' => $movement->getId(),
            'uuid' => $movement->getUuid(),
            'product_id' => $movement->getProductId(),
            'warehouse_id' => $movement->getWarehouseId(),
            'movement_type' => $movement->getMovementType(),
            'quantity' => $movement->getQuantity(),
            'previous_quantity' => $movement->getPreviousQuantity(),
            'new_quantity' => $movement->getNewQuantity(),
            'reference_type' => $movement->getReferenceType(),
            'reference_id' => $movement->getReferenceId(),
            'unit_cost' => $movement->getUnitCost(),
            'total_cost' => $movement->getTotalCost(),
            'notes' => $movement->getNotes(),
            'created_by' => $movement->getCreatedBy(),
            'created_at' => $movement->getCreatedAt()->format('Y-m-d H:i:s'),
        ]);
    }
}
