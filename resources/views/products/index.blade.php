@extends('layouts.app')

@section('title', 'Productos')

@section('page-title', 'Gestión de Productos')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Productos</h5>
            <div class="btn-group">
                <a href="{{ route('web.reportes.productos.excel') }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                </a>
                <a href="{{ route('web.productos.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Nuevo Producto
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Buscar por nombre, SKU o código..." value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="category_id">
                    <option value="">Todas las categorías</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="is_active">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>SKU</th>
                        <th>Producto</th>
                        <th>Categoría</th>
                        <th>Und.</th>
                        <th class="text-center">Stock Mín.</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr class="{{ !$product->isActive ? 'table-secondary' : '' }}">
                        <td>
                            <strong>{{ $product->sku }}</strong>
                            @if($product->barcode)
                                <br><small class="text-muted">{{ $product->barcode }}</small>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $product->name }}</strong>
                            @if($product->description)
                                <br><small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">{{ Str::limit($product->description, 50) }}</small>
                            @endif
                        </td>
                        <td>{{ $product->categoryName ?? '-' }}</td>
                        <td>
                            <span class="badge bg-secondary">{{ $product->unitOfMeasure }}</span>
                        </td>
                        <td class="text-center">{{ $product->minStockLevel }}</td>
                        <td class="text-center">
                            @if($product->isActive)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('web.productos.show', $product->id) }}" class="btn btn-outline-primary" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('web.productos.edit', $product->id) }}" class="btn btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('web.productos.toggle-active', $product->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-{{ $product->isActive ? 'secondary' : 'success' }}" title="{{ $product->isActive ? 'Desactivar' : 'Activar' }}">
                                        <i class="bi bi-{{ $product->isActive ? 'toggle-off' : 'toggle-on' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No hay productos registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
