@extends('layouts.app')

@section('title', 'Proveedores')

@section('page-title', 'Gestión de Proveedores')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Proveedores</h5>
            @can('suppliers.create')
            <a href="{{ route('web.proveedores.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nuevo Proveedor
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Buscar proveedor..." value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="is_active">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ ($filters['is_active'] ?? '') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ ($filters['is_active'] ?? '') === '0' ? 'selected' : '' }}>Inactivos</option>
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
                        <th>Contacto</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th class="text-center">Productos</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suppliers as $supplier)
                    <tr class="{{ !$supplier->isActive ? 'table-secondary' : '' }}">
                        <td>
                            <strong>{{ $supplier->name }}</strong>
                            @if($supplier->taxId)
                                <br><small class="text-muted">NIT: {{ $supplier->taxId }}</small>
                            @endif
                        </td>
                        <td>{{ $supplier->contactName ?? '-' }}</td>
                        <td>{{ $supplier->email ?? '-' }}</td>
                        <td>{{ $supplier->phone ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $supplier->productsCount }}</span>
                        </td>
                        <td class="text-center">
                            @if($supplier->isActive)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('web.proveedores.edit', $supplier->id) }}" class="btn btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('web.proveedores.toggle-active', $supplier->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-{{ $supplier->isActive ? 'secondary' : 'success' }}" title="{{ $supplier->isActive ? 'Desactivar' : 'Activar' }}">
                                        <i class="bi bi-{{ $supplier->isActive ? 'toggle-on' : 'toggle-off' }}"></i>
                                    </button>
                                </form>
                                @can('suppliers.delete')
                                <form action="{{ route('web.proveedores.destroy', $supplier->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este proveedor?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-truck fs-1 d-block mb-2"></i>
                            No hay proveedores registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $suppliers->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
