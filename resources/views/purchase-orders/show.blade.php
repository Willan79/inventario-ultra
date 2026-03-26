@extends('layouts.app')

@section('title', 'Orden de Compra')

@section('page-title', 'Orden de Compra')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Orden: {{ $order->orderNumber }}</h5>
                <div>
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
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <strong>Proveedor:</strong><br>
                        {{ $supplier->name ?? '-' }}
                    </div>
                    <div class="col-md-4">
                        <strong>Fecha de Orden:</strong><br>
                        {{ \Carbon\Carbon::parse($order->orderDate)->format('d/m/Y') }}
                    </div>
                    <div class="col-md-4">
                        <strong>Fecha Esperada:</strong><br>
                        {{ $order->expectedDate ? \Carbon\Carbon::parse($order->expectedDate)->format('d/m/Y') : '-' }}
                    </div>
                </div>

                @if($order->notes)
                <div class="alert alert-secondary">
                    <strong>Notas:</strong> {{ $order->notes }}
                </div>
                @endif

                <h6>Productos</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Producto</th>
                                <th>SKU</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td>{{ $item->productName }}</td>
                                <td>{{ $item->productSku ?? $item->supplierSku ?? '-' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->unitCost, 2) }}</td>
                                <td class="text-end">${{ number_format($item->total, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No hay productos en esta orden</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end">${{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Impuesto:</strong></td>
                                <td class="text-end">${{ number_format($order->taxAmount, 2) }}</td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end">${{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Acciones</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($order->status === 'draft')
                        <form action="{{ route('web.ordenes-compra.send', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-send"></i> Marcar como Enviada
                            </button>
                        </form>
                        <a href="{{ route('web.ordenes-compra.edit', $order->id) }}" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar Orden
                        </a>
                    @endif

                    @if(in_array($order->status, ['draft', 'sent', 'partial']))
                        <form action="{{ route('web.ordenes-compra.receive', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <label for="warehouse_id" class="form-label">Almacén de destino</label>
                                <select class="form-select @error('warehouse_id') is-invalid @enderror" name="warehouse_id" required>
                                    <option value="">Seleccionar almacén...</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-lg"></i> Recibir y Agregar Stock
                            </button>
                        </form>
                    @endif

                    @if(in_array($order->status, ['draft', 'sent']))
                        <form action="{{ route('web.ordenes-compra.cancel', $order->id) }}" method="POST" onsubmit="return confirm('¿Cancelar esta orden?')">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="bi bi-x-lg"></i> Cancelar Orden
                            </button>
                        </form>
                    @endif

                    @if(in_array($order->status, ['draft', 'cancelled']))
                        <form action="{{ route('web.ordenes-compra.destroy', $order->id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta orden?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-trash"></i> Eliminar Orden
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('web.ordenes-compra.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Volver al Listado
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
