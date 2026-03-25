<?php

namespace App\Http\Controllers\Web;

use App\Application\Services\ProductService;
use App\Infrastructure\Persistence\Eloquent\Models\CategoryModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $productService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category_id', 'is_active']);
        $perPage = 15;
        
        $products = $this->productService->getAll($filters, $perPage);
        $categories = CategoryModel::active()->orderBy('name')->get();
        
        return view('products.index', compact('products', 'categories', 'filters'));
    }

    public function create(): View
    {
        $categories = CategoryModel::active()->orderBy('name')->get();
        $units = ['unit' => 'Unidad', 'kg' => 'Kilogramo', 'liter' => 'Litro', 'meter' => 'Metro', 'box' => 'Caja'];
        $costMethods = ['average' => 'Promedio', 'fifo' => 'FIFO', 'lifo' => 'LIFO', 'standard' => 'Estándar'];
        
        return view('products.create', compact('categories', 'units', 'costMethods'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'unit_of_measure' => 'nullable|in:unit,kg,liter,meter,box',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'is_active' => 'nullable|boolean',
            'min_stock_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'cost_method' => 'nullable|in:fifo,lifo,average,standard',
        ]);

        $this->productService->create($validated);
        
        return redirect()->route('web.productos.index')->with('success', 'Producto creado exitosamente');
    }

    public function show(int $id): View
    {
        $product = $this->productService->getById($id);
        
        if (!$product) {
            abort(404);
        }

        $inventories = \App\Infrastructure\Persistence\Eloquent\Models\InventoryModel::with('warehouse')
            ->where('product_id', $id)
            ->get();

        $movements = \App\Infrastructure\Persistence\Eloquent\Models\MovementModel::with('warehouse')
            ->where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('products.show', compact('product', 'inventories', 'movements'));
    }

    public function edit(int $id): View
    {
        $product = $this->productService->getById($id);
        if (!$product) {
            abort(404);
        }
        $categories = CategoryModel::active()->orderBy('name')->get();
        $units = ['unit' => 'Unidad', 'kg' => 'Kilogramo', 'liter' => 'Litro', 'meter' => 'Metro', 'box' => 'Caja'];
        $costMethods = ['average' => 'Promedio', 'fifo' => 'FIFO', 'lifo' => 'LIFO', 'standard' => 'Estándar'];
        
        return view('products.edit', compact('product', 'categories', 'units', 'costMethods'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'sku' => 'sometimes|string|max:50|unique:products,sku,' . $id,
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'unit_of_measure' => 'nullable|in:unit,kg,liter,meter,box',
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $id,
            'is_active' => 'nullable|boolean',
            'min_stock_level' => 'nullable|integer|min:0',
            'max_stock_level' => 'nullable|integer|min:0',
            'reorder_point' => 'nullable|integer|min:0',
            'cost_method' => 'nullable|in:fifo,lifo,average,standard',
        ]);

        $this->productService->update($id, $validated);
        
        return redirect()->route('web.productos.index')->with('success', 'Producto actualizado exitosamente');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->productService->delete($id);
        
        return redirect()->route('web.productos.index')->with('success', 'Producto eliminado exitosamente');
    }

    public function toggleActive(int $id): RedirectResponse
    {
        $this->productService->toggleActive($id);
        
        return redirect()->back()->with('success', 'Estado del producto actualizado');
    }
}
