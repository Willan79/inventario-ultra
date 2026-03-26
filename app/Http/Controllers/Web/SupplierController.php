<?php

namespace App\Http\Controllers\Web;

use App\Application\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function __construct(
        private readonly SupplierService $supplierService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'is_active']);
        $perPage = 15;
        
        $suppliers = $this->supplierService->getAll($filters, $perPage);
        
        return view('suppliers.index', compact('suppliers', 'filters'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $this->supplierService->create($validated);
        
        return redirect()->route('web.proveedores.index')->with('success', 'Proveedor creado exitosamente');
    }

    public function edit(int $id): View
    {
        $supplier = $this->supplierService->getById($id);
        
        if (!$supplier) {
            abort(404);
        }

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $supplier = $this->supplierService->getById($id);
        
        if (!$supplier) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'contact_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'tax_id' => 'nullable|string|max:50',
            'is_active' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $this->supplierService->update($id, $validated);
        
        return redirect()->route('web.proveedores.index')->with('success', 'Proveedor actualizado exitosamente');
    }

    public function destroy(int $id): RedirectResponse
    {
        $supplier = $this->supplierService->getById($id);
        
        if (!$supplier) {
            abort(404);
        }

        $this->supplierService->delete($id);
        
        return redirect()->route('web.proveedores.index')->with('success', 'Proveedor eliminado exitosamente');
    }

    public function toggleActive(int $id): RedirectResponse
    {
        $supplier = $this->supplierService->toggleActive($id);
        
        if (!$supplier) {
            abort(404);
        }

        $status = $supplier->isActive ? 'activado' : 'desactivado';
        return redirect()->back()->with('success', "Proveedor {$status} exitosamente");
    }
}
