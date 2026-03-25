@extends('layouts.app')

@section('title', 'Categorías')

@section('page-title', 'Gestión de Categorías')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Categorías</h5>
            <a href="{{ route('web.categorias.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nueva Categoría
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Buscar categoría..." value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="is_active">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>Activas</option>
                    <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>Inactivas</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Categoría Padre</th>
                        <th class="text-center">Orden</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr class="{{ !$category->isActive ? 'table-secondary' : '' }}">
                        <td>
                            <strong>{{ $category->name }}</strong>
                        </td>
                        <td>
                            <small class="text-muted">{{ Str::limit($category->description, 50) ?? '-' }}</small>
                        </td>
                        <td>
                            @if($category->parentId)
                                <span class="badge bg-info">{{ $category->parent?->name ?? 'N/A' }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $category->sortOrder }}</td>
                        <td class="text-center">
                            @if($category->isActive)
                                <span class="badge bg-success">Activa</span>
                            @else
                                <span class="badge bg-secondary">Inactiva</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('web.categorias.edit', $category->id) }}" class="btn btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('web.categorias.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar esta categoría?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-tags fs-1 d-block mb-2"></i>
                            No hay categorías registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $categories->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
