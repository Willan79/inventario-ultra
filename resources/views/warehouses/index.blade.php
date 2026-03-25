@extends('layouts.app')

@section('title', 'Almacenes')

@section('page-title', 'Gestión de Almacenes')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Almacenes</h5>
            <div class="btn-group">
                <a href="{{ route('web.reportes.almacenes.excel') }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                </a>
                <a href="{{ route('web.almacenes.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Nuevo Almacén
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Buscar por nombre o código..." value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-4">
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
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Ubicación</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($warehouses as $warehouse)
                    <tr class="{{ !$warehouse->isActive ? 'table-secondary' : '' }}">
                        <td>
                            <strong>{{ $warehouse->code }}</strong>
                        </td>
                        <td>
                            <strong>{{ $warehouse->name }}</strong>
                        </td>
                        <td>
                            <i class="bi bi-geo-alt text-muted"></i> {{ $warehouse->location ?? '-' }}
                        </td>
                        <td class="text-center">
                            @if($warehouse->isActive)
                                <span class="badge bg-success">Activo</span>
                            @else
                                <span class="badge bg-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('web.almacenes.show', $warehouse->id) }}" class="btn btn-outline-primary" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('web.almacenes.edit', $warehouse->id) }}" class="btn btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('web.almacenes.toggle-active', $warehouse->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-{{ $warehouse->isActive ? 'secondary' : 'success' }}" title="{{ $warehouse->isActive ? 'Desactivar' : 'Activar' }}">
                                        <i class="bi bi-{{ $warehouse->isActive ? 'toggle-off' : 'toggle-on' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-building fs-1 d-block mb-2"></i>
                            No hay almacenes registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $warehouses->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
