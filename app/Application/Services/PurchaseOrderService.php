<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entities\PurchaseOrder;
use App\Domain\Entities\PurchaseOrderItem;
use App\Domain\Repositories\PurchaseOrderRepositoryInterface;
use App\Domain\Repositories\PurchaseOrderItemRepositoryInterface;
use App\Domain\Repositories\SupplierRepositoryInterface;
use App\Domain\Repositories\ProductRepositoryInterface;
use App\Application\DTOs\PurchaseOrderDTO;
use App\Application\DTOs\PurchaseOrderItemDTO;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PurchaseOrderService
{
    public function __construct(
        private readonly PurchaseOrderRepositoryInterface $orderRepository,
        private readonly PurchaseOrderItemRepositoryInterface $itemRepository,
        private readonly SupplierRepositoryInterface $supplierRepository,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly InventoryService $inventoryService
    ) {}

    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->orderRepository->findAll($filters, $perPage);
    }

    public function getById(int $id): ?PurchaseOrderDTO
    {
        $order = $this->orderRepository->findById($id);
        if (!$order) {
            return null;
        }

        return $this->toDTO($order);
    }

    public function getPending(): Collection
    {
        return $this->orderRepository->findPending()
            ->map(fn($o) => $this->toDTO($o));
    }

    public function create(array $data): PurchaseOrderDTO
    {
        $supplierId = (int) $data['supplier_id'];
        $supplier = $this->supplierRepository->findById($supplierId);
        if (!$supplier) {
            throw new \InvalidArgumentException('Proveedor no encontrado');
        }

        $orderNumber = $this->generateOrderNumber();

        $order = new PurchaseOrder(
            uuid: Str::uuid()->toString(),
            orderNumber: $orderNumber,
            supplierId: $supplierId,
            status: 'draft',
            orderDate: new \DateTimeImmutable($data['order_date']),
            expectedDate: !empty($data['expected_date']) ? new \DateTimeImmutable($data['expected_date']) : null,
            receivedDate: null,
            subtotal: 0,
            taxAmount: (float) ($data['tax_amount'] ?? 0),
            total: 0,
            notes: $data['notes'] ?? null,
            createdBy: $data['created_by']
        );

        $saved = $this->orderRepository->save($order);
        
        if (!empty($data['items']) && is_array($data['items'])) {
            $this->addItems($saved->getId(), $data['items']);
            $this->recalculateTotals($saved->getId());
            $saved = $this->orderRepository->findById($saved->getId());
        }

        return $this->toDTO($saved);
    }

    public function addItems(int $orderId, array $items): void
    {
        foreach ($items as $item) {
            $productId = (int) ($item['product_id'] ?? 0);
            if ($productId <= 0) continue;

            $product = $this->productRepository->findById($productId);
            if (!$product) continue;

            $quantity = (int) ($item['quantity'] ?? 1);
            $unitCost = (float) ($item['unit_cost'] ?? 0);

            $orderItem = new PurchaseOrderItem(
                purchaseOrderId: $orderId,
                productId: $productId,
                quantity: $quantity,
                unitCost: $unitCost,
                supplierSku: $item['supplier_sku'] ?? null,
                notes: $item['notes'] ?? null
            );

            $this->itemRepository->save($orderItem);
        }
    }

    public function removeItem(int $itemId): bool
    {
        $item = $this->itemRepository->findById($itemId);
        if (!$item) return false;

        $orderId = $item->getPurchaseOrderId();
        $this->itemRepository->delete($item);
        $this->recalculateTotals($orderId);

        return true;
    }

    public function recalculateTotals(int $orderId): void
    {
        $order = $this->orderRepository->findById($orderId);
        if (!$order) return;

        $items = $this->itemRepository->findByOrder($orderId);
        $subtotal = $items->sum(fn($i) => $i->getTotal());

        $order = new PurchaseOrder(
            $order->getUuid(),
            $order->getOrderNumber(),
            $order->getSupplierId(),
            $order->getStatus(),
            $order->getOrderDate(),
            $order->getExpectedDate(),
            $order->getReceivedDate(),
            $subtotal,
            $order->getTaxAmount(),
            $subtotal + $order->getTaxAmount(),
            $order->getNotes(),
            $order->getCreatedBy()
        );
        $order->setId($orderId);

        $this->orderRepository->save($order);
    }

    public function updateStatus(int $orderId, string $status): ?PurchaseOrderDTO
    {
        $order = $this->orderRepository->findById($orderId);
        if (!$order) return null;

        $order->updateStatus($status);
        if ($status === 'received') {
            $order->markAsReceived();
        }

        $saved = $this->orderRepository->save($order);
        return $this->toDTO($saved);
    }

    public function markAsSent(int $orderId): ?PurchaseOrderDTO
    {
        return $this->updateStatus($orderId, 'sent');
    }

    public function markAsReceived(int $orderId): ?PurchaseOrderDTO
    {
        return $this->updateStatus($orderId, 'received');
    }

    public function receiveWithStock(int $orderId, int $warehouseId, int $createdBy): ?PurchaseOrderDTO
    {
        $order = $this->orderRepository->findById($orderId);
        if (!$order) {
            return null;
        }

        if (!in_array($order->getStatus(), ['sent', 'partial', 'draft'])) {
            throw new \InvalidArgumentException('La orden no puede ser recibida en su estado actual');
        }

        $items = $this->itemRepository->findByOrder($orderId);
        $supplierId = $order->getSupplierId();
        $orderNumber = $order->getOrderNumber();

        foreach ($items as $item) {
            $this->inventoryService->addStock(
                productId: $item->getProductId(),
                warehouseId: $warehouseId,
                quantity: $item->getQuantity(),
                unitCost: $item->getUnitCost(),
                notes: "Entrada por OC: {$orderNumber}",
                createdBy: $createdBy,
                supplierId: $supplierId,
                referenceType: 'purchase_order',
                referenceId: $orderId
            );
        }

        $order->markAsReceived();
        $saved = $this->orderRepository->save($order);

        return $this->toDTO($saved);
    }

    public function cancel(int $orderId): ?PurchaseOrderDTO
    {
        return $this->updateStatus($orderId, 'cancelled');
    }

    public function getItems(int $orderId): Collection
    {
        return $this->itemRepository->findByOrder($orderId)
            ->map(fn($item) => $this->itemToDTO($item));
    }

    public function delete(int $orderId): bool
    {
        $order = $this->orderRepository->findById($orderId);
        if (!$order) return false;

        $this->itemRepository->deleteByOrder($orderId);
        return $this->orderRepository->delete($order);
    }

    private function generateOrderNumber(): string
    {
        $prefix = 'OC';
        $date = date('Ymd');
        $random = strtoupper(Str::random(4));
        return "{$prefix}-{$date}-{$random}";
    }

    private function toDTO(PurchaseOrder $order): PurchaseOrderDTO
    {
        $supplier = $this->supplierRepository->findById($order->getSupplierId());
        $items = $this->itemRepository->findByOrder($order->getId());

        return PurchaseOrderDTO::fromArray([
            'id' => $order->getId(),
            'uuid' => $order->getUuid(),
            'order_number' => $order->getOrderNumber(),
            'supplier_id' => $order->getSupplierId(),
            'supplier_name' => $supplier?->getName(),
            'status' => $order->getStatus(),
            'order_date' => $order->getOrderDate()->format('Y-m-d'),
            'expected_date' => $order->getExpectedDate()?->format('Y-m-d'),
            'received_date' => $order->getReceivedDate()?->format('Y-m-d'),
            'subtotal' => $order->getSubtotal(),
            'tax_amount' => $order->getTaxAmount(),
            'total' => $order->getTotal(),
            'notes' => $order->getNotes(),
            'created_by' => $order->getCreatedBy(),
            'items_count' => $items->count(),
            'total_items' => $items->sum(fn($i) => $i->getQuantity()),
        ]);
    }

    private function itemToDTO(PurchaseOrderItem $item): PurchaseOrderItemDTO
    {
        $product = $this->productRepository->findById($item->getProductId());

        return PurchaseOrderItemDTO::fromArray([
            'id' => $item->getId(),
            'purchase_order_id' => $item->getPurchaseOrderId(),
            'product_id' => $item->getProductId(),
            'product_name' => $product?->getName(),
            'product_sku' => $product?->getSku()->getValue(),
            'quantity' => $item->getQuantity(),
            'quantity_received' => $item->getQuantityReceived(),
            'unit_cost' => $item->getUnitCost(),
            'total' => $item->getTotal(),
            'supplier_sku' => $item->getSupplierSku(),
            'notes' => $item->getNotes(),
        ]);
    }
}
