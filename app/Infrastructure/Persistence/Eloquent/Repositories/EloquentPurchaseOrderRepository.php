<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Entities\PurchaseOrder;
use App\Domain\Repositories\PurchaseOrderRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Models\PurchaseOrderModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentPurchaseOrderRepository implements PurchaseOrderRepositoryInterface
{
    public function findById(int $id): ?PurchaseOrder
    {
        $model = PurchaseOrderModel::with(['supplier', 'creator', 'items'])->find($id);
        return $model ? $this->toEntity($model) : null;
    }

    public function findByUuid(string $uuid): ?PurchaseOrder
    {
        $model = PurchaseOrderModel::with(['supplier', 'creator', 'items'])->where('uuid', $uuid)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findByOrderNumber(string $orderNumber): ?PurchaseOrder
    {
        $model = PurchaseOrderModel::where('order_number', $orderNumber)->first();
        return $model ? $this->toEntity($model) : null;
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = PurchaseOrderModel::with(['supplier', 'creator']);

        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('order_number', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('order_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('order_date', '<=', $filters['date_to']);
        }

        $query->orderBy('created_at', 'desc');

        $paginator = $query->paginate($perPage);

        $items = collect($paginator->items())->map(function ($model) {
            return \App\Application\DTOs\PurchaseOrderDTO::fromArray([
                'id' => $model->id,
                'uuid' => $model->uuid,
                'order_number' => $model->order_number,
                'supplier_id' => $model->supplier_id,
                'supplier_name' => $model->supplier?->name,
                'status' => $model->status,
                'order_date' => $model->order_date->format('Y-m-d'),
                'expected_date' => $model->expected_date?->format('Y-m-d'),
                'received_date' => $model->received_date?->format('Y-m-d'),
                'subtotal' => $model->subtotal,
                'tax_amount' => $model->tax_amount,
                'total' => $model->total,
                'notes' => $model->notes,
                'created_by' => $model->created_by,
                'created_by_name' => $model->creator?->name,
                'items_count' => $model->items->count(),
                'total_items' => $model->items->sum('quantity'),
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

    public function findBySupplier(int $supplierId): Collection
    {
        return PurchaseOrderModel::where('supplier_id', $supplierId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function findPending(): Collection
    {
        return PurchaseOrderModel::pending()
            ->with('supplier')
            ->orderBy('expected_date')
            ->get()
            ->map(fn($m) => $this->toEntity($m));
    }

    public function save(PurchaseOrder $order): PurchaseOrder
    {
        $model = PurchaseOrderModel::updateOrCreate(
            ['id' => $order->getId()],
            [
                'uuid' => $order->getUuid(),
                'order_number' => $order->getOrderNumber(),
                'supplier_id' => $order->getSupplierId(),
                'status' => $order->getStatus(),
                'order_date' => $order->getOrderDate(),
                'expected_date' => $order->getExpectedDate(),
                'received_date' => $order->getReceivedDate(),
                'subtotal' => $order->getSubtotal(),
                'tax_amount' => $order->getTaxAmount(),
                'total' => $order->getTotal(),
                'notes' => $order->getNotes(),
                'created_by' => $order->getCreatedBy(),
            ]
        );

        return $this->toEntity($model);
    }

    public function delete(PurchaseOrder $order): bool
    {
        $model = PurchaseOrderModel::find($order->getId());
        return $model ? $model->delete() : false;
    }

    private function toEntity(PurchaseOrderModel $model): PurchaseOrder
    {
        $entity = new PurchaseOrder(
            uuid: $model->uuid,
            orderNumber: $model->order_number,
            supplierId: $model->supplier_id,
            status: $model->status,
            orderDate: \DateTimeImmutable::createFromInterface($model->order_date),
            expectedDate: $model->expected_date ? \DateTimeImmutable::createFromInterface($model->expected_date) : null,
            receivedDate: $model->received_date ? \DateTimeImmutable::createFromInterface($model->received_date) : null,
            subtotal: (float) $model->subtotal,
            taxAmount: (float) $model->tax_amount,
            total: (float) $model->total,
            notes: $model->notes,
            createdBy: $model->created_by
        );
        return $entity->setId($model->id);
    }
}
