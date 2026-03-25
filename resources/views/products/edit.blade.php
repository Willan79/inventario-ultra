@extends('layouts.app')

@section('title', 'Editar Producto')

@section('page-title', 'Editar Producto')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Producto</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('web.productos.update', $product->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6">
                            <label for="barcode" class="form-label">Código de Barras</label>
                            <input type="text" class="form-control @error('barcode') is-invalid @enderror" id="barcode" name="barcode" value="{{ old('barcode', $product->barcode) }}">
                            @error('barcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="name" class="form-label">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="category_id" class="form-label">Categoría</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">Sin categoría</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $product->categoryId) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="unit_of_measure" class="form-label">Unidad de Medida</label>
                            <select class="form-select @error('unit_of_measure') is-invalid @enderror" id="unit_of_measure" name="unit_of_measure">
                                @foreach($units as $value => $label)
                                    <option value="{{ $value }}" {{ old('unit_of_measure', $product->unitOfMeasure) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_of_measure')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="min_stock_level" class="form-label">Stock Mínimo</label>
                            <input type="number" class="form-control @error('min_stock_level') is-invalid @enderror" id="min_stock_level" name="min_stock_level" value="{{ old('min_stock_level', $product->minStockLevel) }}" min="0">
                            @error('min_stock_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="max_stock_level" class="form-label">Stock Máximo</label>
                            <input type="number" class="form-control @error('max_stock_level') is-invalid @enderror" id="max_stock_level" name="max_stock_level" value="{{ old('max_stock_level', $product->maxStockLevel) }}" min="0">
                            @error('max_stock_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="cost_method" class="form-label">Método de Costo</label>
                            <select class="form-select @error('cost_method') is-invalid @enderror" id="cost_method" name="cost_method">
                                @foreach($costMethods as $value => $label)
                                    <option value="{{ $value }}" {{ old('cost_method', $product->costMethod) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cost_method')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="is_active" class="form-label">Estado</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $product->isActive) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Producto activo</label>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('web.productos.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Actualizar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
