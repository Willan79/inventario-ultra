@extends('layouts.app')
@section('title', 'Usuarios')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-people"></i> Lista de Usuarios</h5>
        <a href="{{ route('web.usuarios.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Nuevo Usuario
        </a>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('web.usuarios.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Buscar por nombre o email..." value="{{ $search }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @foreach($user->getRoleNames() as $role)
                                @if($role == 'super')
                                    <span class="badge bg-danger">{{ ucfirst($role) }}</span>
                                @elseif($role == 'admin')
                                    <span class="badge bg-success">{{ ucfirst($role) }}</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($role) }}</span>
                                @endif
                            @endforeach
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('web.usuarios.edit', $user->id) }}" 
                                   class="btn btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('web.usuarios.destroy', $user->id) }}" 
                                          onsubmit="return confirm('¿Está seguro de eliminar este usuario?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No hay usuarios registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection
