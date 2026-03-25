<?php

namespace App\Http\Controllers\Web;

use App\Application\Services\WarehouseService;
use App\Infrastructure\Persistence\Eloquent\Models\InventoryModel;
use App\Infrastructure\Persistence\Eloquent\Models\WarehouseModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WarehouseController extends Controller
{
    public function __construct(
        private readonly WarehouseService $warehouseService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'is_active']);
        $perPage = 15;

        $warehouses = $this->warehouseService->getAll($filters, $perPage);

        return view('warehouses.index', compact('warehouses', 'filters'));
    }

    public function create(): View
    {
        return view('warehouses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:warehouses,code',
            'name' => 'required|string|max:100',
            'location' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $this->warehouseService->create($validated);

        return redirect()->route('web.almacenes.index')->with('success', 'Almacén creado exitosamente');
    }

    public function show(int $id): View
    {
        $warehouse = $this->warehouseService->getById($id);

        if (!$warehouse) {
            abort(404);
        }

        $inventories = InventoryModel::with('product')
            ->where('warehouse_id', $id)
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        $stats = [
            'totalProducts' => InventoryModel::where('warehouse_id', $id)->count(),
            'totalQuantity' => InventoryModel::where('warehouse_id', $id)->sum('quantity_available'),
            'totalValue' => InventoryModel::where('warehouse_id', $id)->sum(DB::raw('quantity_available * average_cost')),
        ];

        return view('warehouses.show', compact('warehouse', 'inventories', 'stats'));
    }

    public function edit(int $id): View
    {
        $warehouse = WarehouseModel::findOrFail($id);
        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'code' => 'sometimes|string|max:20|unique:warehouses,code,' . $id,
            'name' => 'sometimes|string|max:100',
            'location' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $this->warehouseService->update($id, $validated);

        return redirect()->route('web.almacenes.index')->with('success', 'Almacén actualizado exitosamente');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->warehouseService->delete($id);

        return redirect()->route('web.almacenes.index')->with('success', 'Almacén eliminado exitosamente');
    }

    public function toggleActive(int $id): RedirectResponse
    {
        $this->warehouseService->toggleActive($id);

        return redirect()->back()->with('success', 'Estado del almacén actualizado');
    }
}
