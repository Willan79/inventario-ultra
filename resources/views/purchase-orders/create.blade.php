@extends('layouts.app')

@section('title', 'Nueva Orden de Compra')

@section('page-title', 'Nueva Orden de Compra')

@section('content')
<div class="row">
    <div class="col-12">
        <form action="{{ route('web.ordenes-compra.store') }}" method="POST" id="orderForm">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-cart3"></i> Datos de la Orden</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="supplier_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                                        <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                            <option value="">Seleccionar proveedor...</option>
                                            @foreach($suppliers as $supplier)
                                                <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                                    {{ $supplier->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('supplier_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="order_date" class="form-label">Fecha de Orden <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('order_date') is-invalid @enderror" id="order_date" name="order_date" value="{{ old('order_date', date('Y-m-d')) }}" required>
                                        @error('order_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="expected_date" class="form-label">Fecha Esperada</label>
                                        <input type="date" class="form-control @error('expected_date') is-invalid @enderror" id="expected_date" name="expected_date" value="{{ old('expected_date') }}">
                                        @error('expected_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0"><i class="bi bi-box-seam"></i> Productos</h5>
                        </div>
                        <div class="card-body">
                            <div id="itemsContainer">
                                @php $oldItems = old('items', []); @endphp
                                @if(count($oldItems) > 0)
                                    @foreach($oldItems as $index => $item)
                                        <div class="row mb-3 item-row" data-index="{{ $index }}">
                                            <div class="col-md-4">
                                                <select class="form-select product-select" name="items[{{ $index }}][product_id]" required>
                                                    <option value="">Seleccionar producto...</option>
                                                    @foreach($products as $p)
                                                        <option value="{{ $p->id }}" {{ ($item['product_id'] ?? '') == $p->id ? 'selected' : '' }}>
                                                            {{ $p->name }} ({{ $p->sku }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" name="items[{{ $index }}][quantity]" placeholder="Cantidad" value="{{ $item['quantity'] ?? '' }}" min="1" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="number" class="form-control" name="items[{{ $index }}][unit_cost]" placeholder="Precio" value="{{ $item['unit_cost'] ?? '' }}" step="0.01" min="0" required>
                                            </div>
                                            <div class="col-md-2">
                                                <input type="text" class="form-control" name="items[{{ $index }}][supplier_sku]" placeholder="SKU Proveedor" value="{{ $item['supplier_sku'] ?? '' }}">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-outline-danger remove-item"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            
                            <button type="button" class="btn btn-outline-primary" id="addItem">
                                <i class="bi bi-plus-lg"></i> Agregar Producto
                            </button>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="notes" class="form-label">Notas</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Resumen</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span id="subtotal">$0.00</span>
                            </div>
                            <div class="mb-3">
                                <label for="tax_amount" class="form-label">Impuesto:</label>
                                <input type="number" class="form-control" id="tax_amount" name="tax_amount" value="{{ old('tax_amount', 0) }}" step="0.01" min="0">
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total:</span>
                                <span id="total">$0.00</span>
                            </div>
                            <hr>
                            <div class="d-grid gap-2">
                                <a href="{{ route('web.ordenes-compra.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg"></i> Crear Orden
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let itemIndex = {{ count(old('items', [])) > 0 ? max(array_keys(old('items', []))) + 1 : 0 }};

const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'sku' => $p->sku]));

function addItemRow() {
    const container = document.getElementById('itemsContainer');
    const row = document.createElement('div');
    row.className = 'row mb-3 item-row';
    row.dataset.index = itemIndex;
    
    let optionsHtml = '<option value="">Seleccionar producto...</option>';
    products.forEach(p => {
        optionsHtml += `<option value="${p.id}">${p.name} (${p.sku})</option>`;
    });
    
    row.innerHTML = `
        <div class="col-md-4">
            <select class="form-select" name="items[${itemIndex}][product_id]" required>
                ${optionsHtml}
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" class="form-control" name="items[${itemIndex}][quantity]" placeholder="Cantidad" min="1" required>
        </div>
        <div class="col-md-2">
            <input type="number" class="form-control" name="items[${itemIndex}][unit_cost]" placeholder="Precio" step="0.01" min="0" required>
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control" name="items[${itemIndex}][supplier_sku]" placeholder="SKU">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger remove-item"><i class="bi bi-trash"></i></button>
        </div>
    `;
    
    container.appendChild(row);
    itemIndex++;
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('input[name*="[quantity]"]')?.value) || 0;
        const cost = parseFloat(row.querySelector('input[name*="[unit_cost]"]')?.value) || 0;
        subtotal += qty * cost;
    });
    
    const tax = parseFloat(document.getElementById('tax_amount')?.value) || 0;
    const total = subtotal + tax;
    
    document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('total').textContent = '$' + total.toFixed(2);
}

document.getElementById('addItem').addEventListener('click', addItemRow);
document.getElementById('itemsContainer').addEventListener('click', function(e) {
    if (e.target.closest('.remove-item')) {
        e.target.closest('.item-row').remove();
        calculateTotals();
    }
});
document.getElementById('itemsContainer').addEventListener('input', calculateTotals);
document.getElementById('tax_amount').addEventListener('input', calculateTotals);

calculateTotals();
</script>
@endpush
@endsection
