@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-dashboard stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-box text-primary fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Total Productos</h6>
                        <h5 class="mb-0">{{ number_format($stats['totalProducts']) }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <small class="text-success"><i class="bi bi-check-circle"></i> {{ $stats['activeProducts'] }} activos</small>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-dashboard stat-card success h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-building text-success fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Almacenes Activos</h6>
                        <h5 class="mb-0">{{ $stats['totalWarehouses'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('web.almacenes.index') }}" class="small text-success text-decoration-none">Ver todos <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-dashboard stat-card warning h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-exclamation-triangle text-warning fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Stock Bajo</h6>
                        <h5 class="mb-0">{{ $stats['lowStockItems'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <a href="{{ route('web.inventario.low-stock') }}" class="small text-warning text-decoration-none">Revisar <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-dashboard stat-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-3">
                            <i class="bi bi-currency-dollar text-info fs-4"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-1 text-muted">Valor Total</h6>
                        <small class="mb-0">${{ number_format($stats['totalValue'], 2) }}</small>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-0 pt-0">
                <small class="text-muted">{{ number_format($stats['totalInventory']) }} unidades</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Movimientos Recientes</h5>
                    <a href="{{ route('web.movimientos.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Producto</th>
                                <th>Almacén</th>
                                <th>Tipo</th>
                                <th class="text-end">Cantidad</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentMovements as $movement)
                            <tr>
                                <td>
                                    <small class="text-muted">{{ $movement->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <strong>{{ $movement->product?->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $movement->product?->sku }}</small>
                                </td>
                                <td>{{ $movement->warehouse?->name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $typeColors = [
                                            'in' => 'success',
                                            'out' => 'danger',
                                            'transfer' => 'info',
                                            'adjustment' => 'warning',
                                            'return' => 'primary'
                                        ];
                                        $typeLabels = [
                                            'in' => 'Entrada',
                                            'out' => 'Salida',
                                            'transfer' => 'Transferencia',
                                            'adjustment' => 'Ajuste',
                                            'return' => 'Devolución'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $typeColors[$movement->movement_type] ?? 'secondary' }}">
                                        {{ $typeLabels[$movement->movement_type] ?? $movement->movement_type }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @php
                                        $isNegative = $movement->movement_type === 'out' || ($movement->movement_type === 'transfer' && !Str::contains($movement->notes, 'desde'));
                                    @endphp
                                    <span class="{{ $isNegative ? 'text-danger' : 'text-success' }}">
                                        {{ $isNegative ? '-' : '+' }}{{ number_format($movement->quantity, 2) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No hay movimientos registrados
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-exclamation-circle"></i> Alerta de Stock</h5>
                    <span class="badge bg-danger">{{ $stats['lowStockItems'] }}</span>
                </div>
            </div>
            <div class="card-body p-0">
                @forelse($lowStockItems as $item)
                <div class="d-flex align-items-center p-3 border-bottom">
                    <div class="flex-shrink-0">
                        <div class="bg-danger bg-opacity-10 rounded p-2">
                            <i class="bi bi-box text-danger"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="mb-0">{{ $item->product?->name ?? 'N/A' }}</h6>
                        <small class="text-muted">
                            {{ $item->warehouse?->name ?? 'N/A' }} |
                            <span class="text-danger">{{ number_format($item->quantity_available - $item->quantity_reserved) }}</span>
                            / {{ $item->product?->min_stock_level ?? 0 }}
                        </small>
                    </div>
                </div>
                @empty
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-check-circle fs-1 text-success d-block mb-2"></i>
                    No hay alertas de stock
                </div>
                @endforelse
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-body text-center">
                <i class="bi bi-graph-up fs-1 text-primary mb-3"></i>
                <h5>Resumen del Día</h5>
                <div class="d-flex justify-content-center gap-4 mt-3">
                    <div>
                        <h3 class="mb-0 text-success">{{ $stats['todayMovements'] }}</h3>
                        <small class="text-muted">Movimientos</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
