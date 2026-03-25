@extends('layouts.app')

@section('title', $warehouse->name)

@section('page-title', $warehouse->name)

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Información</h5>
                    <a href="{{ route('web.almacenes.edit', $warehouse->id) }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-building fs-1 text-primary"></i>
                    </div>
                    <h4>{{ $warehouse->name }}</h4>
                    <p class="text-muted mb-1">{{ $warehouse->code }}</p>
                    @if($warehouse->isActive)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-4 text-muted">Ubicación</dt>
                    <dd class="col-sm-8">{{ $warehouse->location ?? '-' }}</dd>
                    <hr>
                    <dt class="col-sm-4 text-muted">Total Productos</dt>
                    <dd class="col-sm-8">{{ $stats['totalProducts'] }}</dd>
                    <hr>
                    <dt class="col-sm-4 text-muted">Cantidad Total</dt>
                    <dd class="col-sm-8">{{ number_format($stats['totalQuantity'], 2) }}</dd>
                    <hr>
                    <dt class="col-sm-4 text-muted">Valor Total</dt>
                    <dd class="col-sm-8">${{ number_format($stats['totalValue'], 2) }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-collection"></i> Inventario del Almacén</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Costo Prom.</th>
                                <th class="text-end">Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventories as $inv)
                            <tr>
                                <td>
                                    <strong>{{ $inv->product?->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $inv->product?->sku }}</small>
                                </td>
                                <td class="text-end">{{ number_format($inv->quantity_available, 2) }}</td>
                                <td class="text-end">${{ number_format((float)$inv->average_cost, 2) }}</td>
                                <td class="text-end fw-bold">${{ number_format((float)$inv->quantity_available * (float)$inv->average_cost, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No hay inventario en este almacén</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('web.almacenes.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>
@endsection
