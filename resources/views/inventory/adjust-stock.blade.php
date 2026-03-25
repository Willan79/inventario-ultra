@extends('layouts.app')

@section('title', 'Ajustar Stock')

@section('page-title', 'Ajustar Stock')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-sliders text-warning"></i> Ajustar Stock</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('web.inventario.store-adjust-stock') }}" method="POST">
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

                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> El ajuste reemplaza la cantidad actual de inventario por la nueva cantidad especificada.
                    </div>

                    <div class="mb-3">
                        <label for="new_quantity" class="form-label">Nueva Cantidad <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('new_quantity') is-invalid @enderror" 
                            id="new_quantity" name="new_quantity" value="{{ old('new_quantity') }}" 
                            step="0.0001" min="0" required>
                        @error('new_quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Razón del Ajuste <span class="text-danger">*</span></label>
                        <select class="form-select @error('reason') is-invalid @enderror" name="reason" required>
                            <option value="">Seleccione una razón</option>
                            <option value="Ajuste por conteo físico" {{ old('reason') == 'Ajuste por conteo físico' ? 'selected' : '' }}>Ajuste por conteo físico</option>
                            <option value="Producto dañado" {{ old('reason') == 'Producto dañado' ? 'selected' : '' }}>Producto dañado</option>
                            <option value="Producto expirado" {{ old('reason') == 'Producto expirado' ? 'selected' : '' }}>Producto expirado</option>
                            <option value="Pérdida/ Robo" {{ old('reason') == 'Pérdida/ Robo' ? 'selected' : '' }}>Pérdida/ Robo</option>
                            <option value="Corrección de error" {{ old('reason') == 'Corrección de error' ? 'selected' : '' }}>Corrección de error</option>
                            <option value="Otro" {{ old('reason') == 'Otro' ? 'selected' : '' }}>Otro</option>
                        </select>
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('web.inventario.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-check-lg"></i> Ajustar Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
