<?php

namespace App\Http\Controllers\Web;

use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function __construct(
        private readonly EloquentCategoryRepository $categoryRepository
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'is_active']);
        $perPage = 15;
        
        $categories = $this->categoryRepository->findAll($filters, $perPage);
        
        return view('categories.index', compact('categories', 'filters'));
    }

    public function create(): View
    {
        $parentCategories = $this->categoryRepository->findRootCategories();
        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category = new \App\Domain\Entities\Category(
            uuid: Str::uuid()->toString(),
            name: $validated['name'],
            description: $validated['description'] ?? null,
            parentId: $validated['parent_id'] ?? null,
            isActive: $request->has('is_active'),
            sortOrder: (int) ($validated['sort_order'] ?? 0)
        );

        $this->categoryRepository->save($category);
        
        return redirect()->route('web.categorias.index')->with('success', 'Categoría creada exitosamente');
    }

    public function edit(int $id): View
    {
        $category = $this->categoryRepository->findById($id);
        
        if (!$category) {
            abort(404);
        }

        $parentCategories = $this->categoryRepository->findRootCategories();
        
        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $category = $this->categoryRepository->findById($id);
        
        if (!$category) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $category->update(
            name: $validated['name'] ?? null,
            description: $validated['description'] ?? null,
            parentId: $validated['parent_id'] ?? null,
            isActive: $validated['is_active'] ?? null,
            sortOrder: $validated['sort_order'] ?? null
        );

        $this->categoryRepository->save($category);
        
        return redirect()->route('web.categorias.index')->with('success', 'Categoría actualizada exitosamente');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = $this->categoryRepository->findById($id);
        
        if (!$category) {
            abort(404);
        }

        $this->categoryRepository->delete($category);
        
        return redirect()->route('web.categorias.index')->with('success', 'Categoría eliminada exitosamente');
    }
}
