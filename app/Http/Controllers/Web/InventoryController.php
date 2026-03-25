<?php

namespace App\Http\Controllers\Web;

use App\Application\Services\InventoryService;
use App\Infrastructure\Persistence\Eloquent\Models\ProductModel;
use App\Infrastructure\Persistence\Eloquent\Models\WarehouseModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InventoryController extends Controller
{
    public function __construct(
        private readonly InventoryService $inventoryService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['product_id', 'warehouse_id']);
        $perPage = 15;
        
        $inventories = $this->inventoryService->getAll($filters, $perPage);
        $products = ProductModel::active()->orderBy('name')->get();
        $warehouses = WarehouseModel::active()->orderBy('name')->get();
        
        return view('inventory.index', compact('inventories', 'products', 'warehouses', 'filters'));
    }

    public function lowStock(): View
    {
        $inventories = $this->inventoryService->getLowStock();
        
        return view('inventory.low-stock', compact('inventories'));
    }

    public function addStock(Request $request): View
    {
        $products = ProductModel::active()->orderBy('name')->get();
        $warehouses = WarehouseModel::active()->orderBy('name')->get();
        
        return view('inventory.add-stock', compact('products', 'warehouses'));
    }

    public function storeStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0.0001',
            'unit_cost' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->inventoryService->addStock(
                $validated['product_id'],
                $validated['warehouse_id'],
                $validated['quantity'],
                $validated['unit_cost'] ?? null,
                $validated['notes'] ?? null
            );
            
            return redirect()->route('web.inventario.index')->with('success', 'Stock agregado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function removeStock(Request $request): View
    {
        $products = ProductModel::active()->orderBy('name')->get();
        $warehouses = WarehouseModel::active()->orderBy('name')->get();
        
        return view('inventory.remove-stock', compact('products', 'warehouses'));
    }

    public function storeRemoveStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity' => 'required|numeric|min:0.0001',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->inventoryService->removeStock(
                $validated['product_id'],
                $validated['warehouse_id'],
                $validated['quantity'],
                $validated['notes'] ?? null
            );
            
            return redirect()->route('web.inventario.index')->with('success', 'Stock removido exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function adjustStock(Request $request): View
    {
        $products = ProductModel::active()->orderBy('name')->get();
        $warehouses = WarehouseModel::active()->orderBy('name')->get();
        
        return view('inventory.adjust-stock', compact('products', 'warehouses'));
    }

    public function storeAdjustStock(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'new_quantity' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->inventoryService->adjustStock(
                $validated['product_id'],
                $validated['warehouse_id'],
                $validated['new_quantity'],
                $validated['reason']
            );
            
            return redirect()->route('web.inventario.index')->with('success', 'Stock ajustado exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function transfer(Request $request): View
    {
        $products = ProductModel::active()->orderBy('name')->get();
        $warehouses = WarehouseModel::active()->orderBy('name')->get();
        
        return view('inventory.transfer', compact('products', 'warehouses'));
    }

    public function storeTransfer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_warehouse_id' => 'required|exists:warehouses,id|different:to_warehouse_id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'quantity' => 'required|numeric|min:0.0001',
        ]);

        try {
            $this->inventoryService->transfer(
                $validated['product_id'],
                $validated['from_warehouse_id'],
                $validated['to_warehouse_id'],
                $validated['quantity']
            );
            
            return redirect()->route('web.inventario.index')->with('success', 'Transferencia completada exitosamente');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }
}
