@extends('layouts.app')

@section('title', 'Movimientos')

@section('page-title', 'Historial de Movimientos')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Registro de Movimientos</h5>
            <a href="{{ route('web.reportes.movimientos.excel', request()->except('page')) }}" class="btn btn-success btn-sm">
                <i class="bi bi-file-earmark-excel"></i> Exportar Excel
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="product_id" class="form-label small">Producto</label>
                    <select class="form-select form-select-sm" name="product_id">
                        <option value="">Todos</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ ($filters['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="warehouse_id" class="form-label small">Almacén</label>
                    <select class="form-select form-select-sm" name="warehouse_id">
                        <option value="">Todos</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}" {{ ($filters['warehouse_id'] ?? '') == $warehouse->id ? 'selected' : '' }}>
                                {{ $warehouse->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="movement_type" class="form-label small">Tipo</label>
                    <select class="form-select form-select-sm" name="movement_type">
                        <option value="">Todos</option>
                        <option value="in" {{ ($filters['movement_type'] ?? '') === 'in' ? 'selected' : '' }}>Entrada</option>
                        <option value="out" {{ ($filters['movement_type'] ?? '') === 'out' ? 'selected' : '' }}>Salida</option>
                        <option value="transfer" {{ ($filters['movement_type'] ?? '') === 'transfer' ? 'selected' : '' }}>Transferencia</option>
                        <option value="adjustment" {{ ($filters['movement_type'] ?? '') === 'adjustment' ? 'selected' : '' }}>Ajuste</option>
                        <option value="return" {{ ($filters['movement_type'] ?? '') === 'return' ? 'selected' : '' }}>Devolución</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label for="date_from" class="form-label small">Desde</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
                </div>
                
                <div class="col-md-2">
                    <label for="date_to" class="form-label small">Hasta</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
                </div>
                
                <div class="col-md-2">
                    <div class="btn-group w-100" role="group">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-search"></i> Filtrar
                        </button>
                        <a href="{{ route('web.movimientos.index') }}" class="btn btn-secondary btn-sm" title="Limpiar filtros">
                            <i class="bi bi-x-circle"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="row g-2 mt-2">
                <div class="col-auto">
                    <div class="btn-group btn-group-sm" role="group">
                        <a href="{{ request()->fullUrlWithQuery(['date_range' => 'today']) }}" 
                           class="btn {{ ($filters['date_range'] ?? '') === 'today' ? 'btn-primary' : 'btn-outline-secondary' }} btn-sm">
                            Hoy
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['date_range' => 'week']) }}" 
                           class="btn {{ ($filters['date_range'] ?? '') === 'week' ? 'btn-primary' : 'btn-outline-secondary' }} btn-sm">
                            Esta semana
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['date_range' => 'month']) }}" 
                           class="btn {{ ($filters['date_range'] ?? '') === 'month' ? 'btn-primary' : 'btn-outline-secondary' }} btn-sm">
                            Este mes
                        </a>
                    </div>
                </div>
            </div>
        </form>

        @if(count(array_filter($filters)))
            <div class="alert alert-light border small mb-3">
                <i class="bi bi-funnel"></i> Filtros activos:
                @if(isset($filters['product_id']))
                    <span class="badge bg-primary">Producto</span>
                @endif
                @if(isset($filters['warehouse_id']))
                    <span class="badge bg-primary">Almacén</span>
                @endif
                @if(isset($filters['movement_type']))
                    <span class="badge bg-primary">{{ $filters['movement_type'] }}</span>
                @endif
                @if(isset($filters['date_from']))
                    <span class="badge bg-info">Desde: {{ $filters['date_from'] }}</span>
                @endif
                @if(isset($filters['date_to']))
                    <span class="badge bg-info">Hasta: {{ $filters['date_to'] }}</span>
                @endif
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Producto</th>
                        <th>Almacén</th>
                        <th>Tipo</th>
                        <th class="text-end">Cantidad</th>
                        <th class="text-end">Stock Anterior</th>
                        <th class="text-end">Stock Nuevo</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movements as $mov)
                    <tr>
                        <td>
                            <small class="text-muted">{{ $mov->created_at->format('d/m/Y H:i') }}</small>
                        </td>
                        <td>
                            <strong>{{ $mov->product?->name ?? 'N/A' }}</strong>
                            <br><small class="text-muted">{{ $mov->product?->sku }}</small>
                        </td>
                        <td>
                            <strong>{{ $mov->warehouse?->name ?? 'N/A' }}</strong>
                        </td>
                        <td>
                            @php
                                $colors = ['in' => 'success', 'out' => 'danger', 'transfer' => 'info', 'adjustment' => 'warning', 'return' => 'primary'];
                                $labels = ['in' => 'Entrada', 'out' => 'Salida', 'transfer' => 'Transf.', 'adjustment' => 'Ajuste', 'return' => 'Dev.'];
                            @endphp
                            <span class="badge bg-{{ $colors[$mov->movement_type] ?? 'secondary' }}">
                                {{ $labels[$mov->movement_type] ?? $mov->movement_type }}
                            </span>
                        </td>
                        <td class="text-end {{ $mov->movement_type === 'out' || ($mov->movement_type === 'transfer' && !Str::contains($mov->notes, 'desde')) ? 'text-danger' : 'text-success' }}">
                            @php
                                $isNegative = $mov->movement_type === 'out' || ($mov->movement_type === 'transfer' && !Str::contains($mov->notes, 'desde'));
                            @endphp
                            <strong>{{ $isNegative ? '-' : '+' }}{{ number_format($mov->quantity, 2) }}</strong>
                        </td>
                        <td class="text-end text-muted">{{ number_format($mov->previous_quantity, 2) }}</td>
                        <td class="text-end">{{ number_format($mov->new_quantity, 2) }}</td>
                        <td><small class="text-muted">{{ Str::limit($mov->notes, 30) }}</small></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No hay movimientos registrados
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted small">
                Mostrando {{ $movements->firstItem() ?? 0 }} - {{ $movements->lastItem() ?? 0 }} de {{ $movements->total() }} registros
            </div>
            <div>
                <form method="GET" class="d-inline-flex align-items-center gap-2">
                    <span class="text-muted small">Por página:</span>
                    <select name="per_page" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                        <option value="10" {{ $movements->perPage() == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $movements->perPage() == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $movements->perPage() == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $movements->perPage() == 100 ? 'selected' : '' }}>100</option>
                    </select>
                    @foreach(request()->except('per_page') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                </form>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-2">
            {{ $movements->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
