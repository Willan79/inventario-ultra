@extends('layouts.app')

@section('title', $product->name)

@section('page-title', $product->name)

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Información</h5>
                    <a href="{{ route('web.productos.edit', $product->id) }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="bi bi-box fs-1 text-primary"></i>
                    </div>
                    <h4>{{ $product->name }}</h4>
                    <p class="text-muted mb-1">{{ $product->sku }}</p>
                    @if($product->isActive)
                        <span class="badge bg-success">Activo</span>
                    @else
                        <span class="badge bg-secondary">Inactivo</span>
                    @endif
                </div>

                <dl class="row mb-0">
                    <dt class="col-sm-5 text-muted">Categoría</dt>
                    <dd class="col-sm-7">{{ $product->categoryName ?? '-' }}</dd>
                    
                    <dt class="col-sm-5 text-muted">Und. Medida</dt>
                    <dd class="col-sm-7"><span class="badge bg-secondary">{{ $product->unitOfMeasure }}</span></dd>
                    
                    <dt class="col-sm-5 text-muted">Código Barra</dt>
                    <dd class="col-sm-7">{{ $product->barcode ?? '-' }}</dd>
                    
                    <dt class="col-sm-5 text-muted">Método Costo</dt>
                    <dd class="col-sm-7">{{ strtoupper($product->costMethod) }}</dd>
                    
                    <dt class="col-sm-5 text-muted">Stock Mínimo</dt>
                    <dd class="col-sm-7">{{ $product->minStockLevel }}</dd>
                    
                    <dt class="col-sm-5 text-muted">Pto. Reorden</dt>
                    <dd class="col-sm-7">{{ $product->reorderPoint }}</dd>
                </dl>

                @if($product->description)
                    <hr>
                    <p class="text-muted small">{{ $product->description }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-collection"></i> Stock por Almacén</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Almacén</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Costo Prom.</th>
                                <th class="text-end">Valor Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($inventories as $inv)
                            <tr>
                                <td>
                                    <strong>{{ $inv->warehouse?->name ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $inv->warehouse?->code }}</small>
                                </td>
                                <td class="text-end">{{ number_format($inv->quantity_available, 2) }}</td>
                                <td class="text-end">${{ number_format((float)$inv->average_cost, 2) }}</td>
                                <td class="text-end fw-bold">${{ number_format((float)$inv->quantity_available * (float)$inv->average_cost, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No hay inventario registrado</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Últimos Movimientos</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Almacén</th>
                                <th>Tipo</th>
                                <th class="text-end">Cantidad</th>
                                <th>Notas</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($movements as $mov)
                            <tr>
                                <td><small>{{ $mov->created_at->format('d/m/Y H:i') }}</small></td>
                                <td>{{ $mov->warehouse?->name ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $colors = ['in' => 'success', 'out' => 'danger', 'transfer' => 'info', 'adjustment' => 'warning', 'return' => 'primary'];
                                        $labels = ['in' => 'Entrada', 'out' => 'Salida', 'transfer' => 'Transf.', 'adjustment' => 'Ajuste', 'return' => 'Dev.'];
                                    @endphp
                                    <span class="badge bg-{{ $colors[$mov->movement_type] ?? 'secondary' }}">
                                        {{ $labels[$mov->movement_type] ?? $mov->movement_type }}
                                    </span>
                                </td>
                                <td class="text-end {{ $mov->movement_type === 'out' ? 'text-danger' : 'text-success' }}">
                                    {{ $mov->movement_type === 'out' ? '-' : '+' }}{{ number_format($mov->quantity, 2) }}
                                </td>
                                <td><small class="text-muted">{{ Str::limit($mov->notes, 30) }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay movimientos registrados</td>
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
    <a href="{{ route('web.productos.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
</div>
@endsection
