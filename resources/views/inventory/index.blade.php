@extends('layouts.app')

@section('title', 'Inventario')

@section('page-title', 'Gestión de Inventario')

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Inventario General</h5>
            <div class="btn-group">
                <a href="{{ route('web.inventario.add-stock') }}" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-circle"></i> Agregar Stock
                </a>
                <a href="{{ route('web.inventario.remove-stock') }}" class="btn btn-danger btn-sm">
                    <i class="bi bi-dash-circle"></i> Remover Stock
                </a>
                <a href="{{ route('web.inventario.adjust-stock') }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-sliders"></i> Ajustar
                </a>
                <a href="{{ route('web.reportes.inventario.excel') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                </a>
            </div>
                </a>
                <a href="{{ route('web.inventario.transfer') }}" class="btn btn-info btn-sm">
                    <i class="bi bi-arrow-left-right"></i> Transferir
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-4">
                <select class="form-select" name="product_id">
                    <option value="">Todos los productos</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ ($filters['product_id'] ?? '') == $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->sku }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select class="form-select" name="warehouse_id">
                    <option value="">Todos los almacenes</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ ($filters['warehouse_id'] ?? '') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary w-100">Filtrar</button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Producto</th>
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
                            <strong>{{ $inv->productName ?? 'N/A' }}</strong>
                            <br><small class="text-muted">{{ $inv->productSku ?? '' }}</small>
                        </td>
                        <td>
                            <strong>{{ $inv->warehouseName ?? 'N/A' }}</strong>
                            <br><small class="text-muted">{{ $inv->warehouseCode ?? '' }}</small>
                        </td>
                        <td class="text-end">{{ number_format($inv->quantityAvailable, 2) }}</td>
                        <td class="text-end">${{ number_format($inv->averageCost, 2) }}</td>
                        <td class="text-end fw-bold">${{ number_format($inv->quantityAvailable * $inv->averageCost, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No hay inventario registrado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $inventories->withQueryString()->links() }}
        </div>
    </div>
</div>
@endsection
