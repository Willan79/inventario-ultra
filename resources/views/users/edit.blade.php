@extends('layouts.app')
@section('title', 'Editar Usuario')
@section('page-title', 'Editar Usuario')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-pencil-square"></i> Editar Usuario</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('web.usuarios.update', $user->id) }}">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nombre Completo</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Correo Electrónico</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Nueva Contraseña <small class="text-muted">(opcional)</small></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                           id="password" name="password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                    <input type="password" class="form-control" 
                           id="password_confirmation" name="password_confirmation">
                </div>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Rol</label>
                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                    <option value="super" {{ $user->hasRole('super') ? 'selected' : '' }}>
                        Super Administrador (todos los permisos)
                    </option>
                    <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>
                        Administrador (sin gestión de usuarios)
                    </option>
                    <option value="user" {{ $user->hasRole('user') ? 'selected' : '' }}>
                        Usuario (solo lectura)
                    </option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('web.usuarios.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Actualizar Usuario
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
