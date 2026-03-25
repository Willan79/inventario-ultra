@extends('layouts.app')

@section('title', 'Agregar Stock')

@section('page-title', 'Agregar Stock')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-plus-circle text-success"></i> Agregar Stock</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('web.inventario.store-stock') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="product_id" class="form-label">Producto <span class="text-danger">*</span></label>
                        <select class="form-select @error('product_id') is-invalid @enderror" name="product_id" required>
                            <option value="">Seleccione un producto</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="warehouse_id" class="form-label">Almacén <span class="text-danger">*</span></label>
                        <select class="form-select @error('warehouse_id') is-invalid @enderror" name="warehouse_id" required>
                            <option value="">Seleccione un almacén</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Cantidad <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                    id="quantity" name="quantity" value="{{ old('quantity') }}" 
                                    step="0.0001" min="0.0001" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="unit_cost" class="form-label">Costo Unitario</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('unit_cost') is-invalid @enderror" 
                                        id="unit_cost" name="unit_cost" value="{{ old('unit_cost') }}" 
                                        step="0.01" min="0">
                                </div>
                                @error('unit_cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Notas</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                            id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('web.inventario.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg"></i> Agregar Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
