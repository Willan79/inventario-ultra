<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Inventario')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
    <style>
        :root {
            --primary: #0d6efd;
            --success: #198754;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #0dcaf0;
        }
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 4px 8px;
            transition: all 0.3s;
        }
        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .sidebar .nav-link.active {
            background: var(--primary);
            color: #fff;
        }
        .sidebar .nav-link i {
            width: 24px;
        }
        .card-dashboard {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card-dashboard:hover {
            transform: translateY(-2px);
        }
        .stat-card {
            border-left: 4px solid var(--primary);
        }
        .stat-card.success { border-left-color: var(--success); }
        .stat-card.warning { border-left-color: var(--warning); }
        .stat-card.danger { border-left-color: var(--danger); }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }
        .btn-group-sm > .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        .badge-type {
            font-size: 0.7rem;
            padding: 0.35em 0.65em;
        }
        .role-badge {
            font-size: 0.65rem;
            padding: 0.25em 0.5em;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="container-fluid p-0">
        <div class="row g-0">
            <nav class="col-md-2 col-lg-2 sidebar d-none d-md-block">
                <div class="p-3 text-white">
                    <h4 class="mb-0"><i class="bi bi-box-seam"></i> Inventario</h4>
                    <small class="text-muted">Sistema Empresarial</small>
                </div>
                <ul class="nav flex-column mb-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.dashboard') ? 'active' : '' }}" href="{{ route('web.dashboard') }}">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    @can('products.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.productos.*') ? 'active' : '' }}" href="{{ route('web.productos.index') }}">
                            <i class="bi bi-box"></i> Productos
                        </a>
                    </li>
                    @endcan
                    @can('warehouses.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.almacenes.*') ? 'active' : '' }}" href="{{ route('web.almacenes.index') }}">
                            <i class="bi bi-building"></i> Almacenes
                        </a>
                    </li>
                    @endcan
                    @can('inventory.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.inventario.*') ? 'active' : '' }}" href="{{ route('web.inventario.index') }}">
                            <i class="bi bi-collection"></i> Inventario
                        </a>
                    </li>
                    @endcan
                    @can('movements.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.movimientos.*') ? 'active' : '' }}" href="{{ route('web.movimientos.index') }}">
                            <i class="bi bi-arrow-left-right"></i> Movimientos
                        </a>
                    </li>
                    @endcan
                    @can('categories.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.categorias.*') ? 'active' : '' }}" href="{{ route('web.categorias.index') }}">
                            <i class="bi bi-tags"></i> Categorías
                        </a>
                    </li>
                    @endcan
                    @can('suppliers.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.proveedores.*') ? 'active' : '' }}" href="{{ route('web.proveedores.index') }}">
                            <i class="bi bi-truck"></i> Proveedores
                        </a>
                    </li>
                    @endcan
                    @can('suppliers.view')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.ordenes-compra.*') ? 'active' : '' }}" href="{{ route('web.ordenes-compra.index') }}">
                            <i class="bi bi-cart3"></i> Órdenes de Compra
                        </a>
                    </li>
                    @endcan
                    @role('super|admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('web.usuarios.*') ? 'active' : '' }}" href="{{ route('web.usuarios.index') }}">
                            <i class="bi bi-people"></i> Usuarios
                        </a>
                    </li>
                    @endrole
                </ul>
                <hr class="border-secondary">
                <div class="px-3 text-white-50">
                    <small>Conectado como:</small>
                    <div class="fw-bold text-white">{{ auth()->user()->name }}</div>
                    @foreach(auth()->user()->getRoleNames() as $role)
                        @if($role == 'super')
                            <span class="badge bg-danger role-badge">{{ $role }}</span>
                        @elseif($role == 'admin')
                            <span class="badge bg-success role-badge">{{ $role }}</span>
                        @else
                            <span class="badge bg-secondary role-badge">{{ $role }}</span>
                        @endif
                    @endforeach
                </div>
            </nav>

            <main class="col-12 col-md-10 ms-sm-auto px-md-4 bg-light min-vh-100">
                <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm rounded mb-4">
                    <div class="container-fluid">
                        <button class="btn btn-outline-secondary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                            <i class="bi bi-list"></i>
                        </button>
                        <span class="navbar-brand mb-0 h1 d-none d-sm-inline">@yield('page-title', 'Dashboard')</span>
                        <div class="d-flex">
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <span class="dropdown-item-text">
                                            <small class="text-muted">
                                                @foreach(auth()->user()->getRoleNames() as $role)
                                                    <span class="badge bg-{{ $role == 'super' ? 'danger' : ($role == 'admin' ? 'success' : 'secondary') }}">
                                                        {{ ucfirst($role) }}
                                                    </span>
                                                @endforeach
                                            </small>
                                        </span>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="POST" action="{{ route('web.logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @stack('scripts')
</body>
</html>
