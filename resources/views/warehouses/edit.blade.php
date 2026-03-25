@extends('layouts.app')

@section('title', 'Editar Almacén')

@section('page-title', 'Editar Almacén')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-pencil"></i> Editar Almacén</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('web.almacenes.update', $warehouse->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="code" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $warehouse->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $warehouse->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Ubicación</label>
                        <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $warehouse->location) }}">
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $warehouse->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Almacén activo</label>
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('web.almacenes.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Actualizar Almacén
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
