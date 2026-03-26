@extends('layouts.app')

@section('title', 'Órdenes de Compra')

@section('page-title', 'Órdenes de Compra')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Listado de Órdenes de Compra</h5>
            @can('suppliers.create')
            <a href="{{ route('web.ordenes-compra.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Nueva Orden
            </a>
            @endcan
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" name="search" placeholder="Nº Orden..." value="{{ $filters['search'] ?? '' }}">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select" name="supplier_id">
                    <option value="">Todos los proveedores</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ ($filters['supplier_id'] ?? '') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Todos los estados</option>
                    <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Borrador</option>
                    <option value="sent" {{ ($filters['status'] ?? '') === 'sent' ? 'selected' : '' }}>Enviada</option>
                    <option value="partial" {{ ($filters['status'] ?? '') === 'partial' ? 'selected' : '' }}>Parcial</option>
                    <option value="received" {{ ($filters['status'] ?? '') === 'received' ? 'selected' : '' }}>Recibida</option>
                    <option value="cancelled" {{ ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
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
                        <th>Nº Orden</th>
                        <th>Proveedor</th>
                        <th>Fecha</th>
                        <th class="text-center">Items</th>
                        <th class="text-end">Total</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->orderNumber }}</strong>
                        </td>
                        <td>{{ $order->supplierName ?? '-' }}</td>
                        <td>{{ \Carbon\Carbon::parse($order->orderDate)->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $order->totalItems }}</td>
                        <td class="text-end">{{ number_format($order->total, 2) }}</td>
                        <td class="text-center">
                            @switch($order->status)
                                @case('draft')
                                    <span class="badge bg-secondary">Borrador</span>
                                @break
                                @case('sent')
                                    <span class="badge bg-primary">Enviada</span>
                                @break
                                @case('partial')
                                    <span class="badge bg-warning">Parcial</span>
                                @break
                                @case('received')
                                    <span class="badge bg-success">Recibida</span>
                                @break
                                @case('cancelled')
                                    <span class="badge bg-danger">Cancelada</span>
                                @break
                            @endswitch
                        </td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('web.ordenes-compra.show', $order->id) }}" class="btn btn-outline-primary" title="Ver">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($order->status === 'draft')
                                    <a href="{{ route('web.ordenes-compra.edit', $order->id) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
                            No hay órdenes de compra registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $orders->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
