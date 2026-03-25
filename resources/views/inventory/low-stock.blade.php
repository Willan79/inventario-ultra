@extends('layouts.app')

@section('title', 'Stock Bajo')

@section('page-title', 'Productos con Stock Bajo')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-exclamation-triangle text-warning"></i> Alerta de Stock Bajo</h5>
            <a href="{{ route('web.inventario.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
                        <th>Almacén</th>
                        <th class="text-end">Disponible</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $inv)
                    <tr class="{{ ($inv->quantityAvailable - $inv->quantityReserved) < 1 ? 'table-danger' : 'table-warning' }}">
                        <td>
                            <strong>{{ $inv->productName ?? 'N/A' }}</strong>
                            <br><small class="text-muted">{{ $inv->productSku ?? '' }}</small>
                        </td>
                        <td>
                            <strong>{{ $inv->warehouseName ?? 'N/A' }}</strong>
                        </td>
                        <td class="text-end">
                            <span class="fw-bold text-danger">{{ number_format($inv->quantityAvailable - $inv->quantityReserved, 2) }}</span>
                        </td>
                        <td class="text-center">
                            @if(($inv->quantityAvailable - $inv->quantityReserved) < 1)
                                <span class="badge bg-danger">Sin Stock</span>
                            @else
                                <span class="badge bg-warning">Bajo Stock</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('web.inventario.add-stock') }}" class="btn btn-sm btn-success">
                                <i class="bi bi-plus"></i> Reabastecer
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                            No hay productos con stock bajo
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
