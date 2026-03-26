<?php

namespace App\Http\Controllers\Web;

use App\Application\Services\PurchaseOrderService;
use App\Application\Services\SupplierService;
use App\Application\Services\ProductService;
use App\Application\Services\WarehouseService;
use App\Infrastructure\Persistence\Eloquent\Models\PurchaseOrderModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PurchaseOrderController extends Controller
{
    public function __construct(
        private readonly PurchaseOrderService $orderService,
        private readonly SupplierService $supplierService,
        private readonly ProductService $productService,
        private readonly WarehouseService $warehouseService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'supplier_id', 'status', 'date_from', 'date_to']);
        $perPage = 15;
        
        $orders = $this->orderService->getAll($filters, $perPage);
        $suppliers = $this->supplierService->getActive();
        
        return view('purchase-orders.index', compact('orders', 'filters', 'suppliers'));
    }

    public function create(): View
    {
        $suppliers = $this->supplierService->getActive();
        $products = $this->productService->search('')->take(20);
        
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.supplier_sku' => 'nullable|string|max:100',
        ]);

        $validated['created_by'] = auth()->id();

        try {
            $order = $this->orderService->create($validated);
            return redirect()->route('web.ordenes-compra.show', $order->id)
                ->with('success', 'Orden de compra creada exitosamente');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(int $id): View
    {
        $order = $this->orderService->getById($id);
        
        if (!$order) {
            abort(404);
        }

        $items = $this->orderService->getItems($id);
        $supplier = $this->supplierService->getById($order->supplierId);
        $warehouses = $this->warehouseService->getActive();
        
        return view('purchase-orders.show', compact('order', 'items', 'supplier', 'warehouses'));
    }

    public function edit(int $id): View
    {
        $order = $this->orderService->getById($id);
        
        if (!$order) {
            abort(404);
        }

        if (!in_array($order->status, ['draft'])) {
            return redirect()->route('web.ordenes-compra.show', $id)
                ->with('error', 'Solo se pueden editar órdenes en estado borrador');
        }

        $suppliers = $this->supplierService->getActive();
        $items = $this->orderService->getItems($id);
        $products = $this->productService->search('')->take(20);
        
        return view('purchase-orders.edit', compact('order', 'suppliers', 'items', 'products'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $order = $this->orderService->getById($id);
        
        if (!$order) {
            abort(404);
        }

        if (!in_array($order->status, ['draft'])) {
            return redirect()->back()->with('error', 'Solo se pueden editar órdenes en estado borrador');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $data = array_merge($validated, ['created_by' => auth()->id()]);

        if (!empty($request->items)) {
            $items = $request->validate([
                'items' => 'array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.unit_cost' => 'required|numeric|min:0',
            ]);
            $data['items'] = $items['items'];
        }

        return redirect()->route('web.ordenes-compra.show', $id)
            ->with('success', 'Orden de compra actualizada');
    }

    public function destroy(int $id): RedirectResponse
    {
        $order = $this->orderService->getById($id);
        
        if (!$order) {
            abort(404);
        }

        if (!in_array($order->status, ['draft', 'cancelled'])) {
            return redirect()->back()->with('error', 'No se puede eliminar una orden recibida o parcialmente recibida');
        }

        $this->orderService->delete($id);
        
        return redirect()->route('web.ordenes-compra.index')
            ->with('success', 'Orden de compra eliminada');
    }

    public function send(int $id): RedirectResponse
    {
        $order = $this->orderService->markAsSent($id);
        
        if (!$order) {
            abort(404);
        }

        return redirect()->back()->with('success', 'Orden marcada como enviada');
    }

    public function receive(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
        ]);

        try {
            $order = $this->orderService->receiveWithStock(
                orderId: $id,
                warehouseId: (int) $validated['warehouse_id'],
                createdBy: auth()->id()
            );
            
            if (!$order) {
                abort(404);
            }

            return redirect()->back()->with('success', 'Orden recibida y stock agregado exitosamente');
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function cancel(int $id): RedirectResponse
    {
        $order = $this->orderService->cancel($id);
        
        if (!$order) {
            abort(404);
        }

        return redirect()->back()->with('success', 'Orden cancelada');
    }
}
