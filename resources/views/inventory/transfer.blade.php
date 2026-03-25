@extends('layouts.app')

@section('title', 'Transferir Stock')

@section('page-title', 'Transferir Stock entre Almacenes')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-arrow-left-right text-info"></i> Transferir Stock</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('web.inventario.store-transfer') }}" method="POST">
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="from_warehouse_id" class="form-label">Almacén Origen <span class="text-danger">*</span></label>
                                <select class="form-select @error('from_warehouse_id') is-invalid @enderror" name="from_warehouse_id" required>
                                    <option value="">Origen</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('from_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('from_warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="to_warehouse_id" class="form-label">Almacén Destino <span class="text-danger">*</span></label>
                                <select class="form-select @error('to_warehouse_id') is-invalid @enderror" name="to_warehouse_id" required>
                                    <option value="">Destino</option>
                                    @foreach($warehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                            {{ $warehouse->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('to_warehouse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="quantity" class="form-label">Cantidad a Transferir <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                            id="quantity" name="quantity" value="{{ old('quantity') }}" 
                            step="0.0001" min="0.0001" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('web.inventario.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-info">
                            <i class="bi bi-check-lg"></i> Transferir
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
