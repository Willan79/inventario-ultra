# Inventario Ultra

Sistema de gestión de inventario empresarial desarrollado con Laravel 12.

## Requisitos

- PHP 8.2+
- Composer
- MySQL 8.0+
- Node.js 18+ (opcional, para assets)

## Instalación

1. **Clonar el repositorio**
```bash
git clone <repo-url>
cd inventario_ultra
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar entorno**
```bash
cp .env.example .env
```

4. **Configurar base de datos** en `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario_ultra
DB_USERNAME=root
DB_PASSWORD=
```

5. **Generar clave de aplicación**
```bash
php artisan key:generate
```

6. **Ejecutar migraciones y seeders**
```bash
php artisan migrate
php artisan db:seed
```

7. **Iniciar el servidor**
```bash
php artisan serve
```

8. **Acceder a la aplicación**
```
http://localhost:8000
```

## Credenciales por defecto

| Rol | Email | Contraseña |
|-----|-------|------------|
| Super Admin | admin@example.com | password |
| Admin | manager@example.com | password |
| Usuario | user@example.com | password |

## Estructura del Proyecto

```
app/
├── Application/           # Capa de aplicación
│   ├── DTOs/            # Data Transfer Objects
│   └── Services/         # Servicios de negocio
├── Domain/               # Capa de dominio
│   ├── Entities/         # Entidades de negocio
│   ├── Exceptions/       # Excepciones del dominio
│   ├── Repositories/     # Interfaces de repositorio
│   └── ValueObjects/    # Objetos de valor
├── Http/
│   └── Controllers/
│       └── Web/          # Controladores web
├── Infrastructure/       # Capa de infraestructura
│   └── Persistence/
│       └── Eloquent/
│           ├── Models/   # Modelos Eloquent
│           └── Repositories/ # Implementaciones
└── Models/               # Modelos principales (User)
```

## Módulos del Sistema

### Productos
Gestión de productos con SKU, código de barras, categorías y niveles de stock.

**Permisos:** `products.view`, `products.create`, `products.update`, `products.delete`

### Categorías
Categorías jerárquicas para organizar productos.

**Permisos:** `categories.view`, `categories.create`, `categories.update`, `categories.delete`

### Almacenes
Control de múltiples almacenes/ubicaciones.

**Permisos:** `warehouses.view`, `warehouses.create`, `warehouses.update`, `warehouses.delete`

### Inventario
Control de stock con operaciones de:
- Agregar stock
- Remover stock
- Ajustar stock
- Transferir entre almacenes
- Stock bajo

**Permisos:** `inventory.view`, `inventory.add`, `inventory.remove`, `inventory.adjust`, `inventory.transfer`

### Proveedores
Gestión de proveedores y relación con productos.

**Permisos:** `suppliers.view`, `suppliers.create`, `suppliers.update`, `suppliers.delete`

### Órdenes de Compra
Creación y gestión de órdenes de compra a proveedores.

**Flujo:** Borrador → Enviada → Recibida → Stock agregado automáticamente

**Permisos:** `suppliers.view`, `suppliers.create`, `suppliers.update`, `suppliers.delete`

### Movimientos
Historial completo de todas las operaciones de stock.

**Permisos:** `movements.view`

### Usuarios
Gestión de usuarios y roles (solo super admin).

**Permisos:** `users.view`, `users.create`, `users.update`, `users.delete`

## Roles y Permisos

| Rol | Descripción | Permisos |
|-----|-------------|----------|
| **super** | Administrador total | Todos |
| **admin** | Administrador sin gestión de usuarios | Todos excepto usuarios |
| **user** | Solo lectura | Ver módulos |

## Base de Datos

### Tablas principales

- `users` - Usuarios del sistema
- `products` - Catálogo de productos
- `categories` - Categorías jerárquicas
- `warehouses` - Almacenes/ubicaciones
- `inventories` - Stock por producto y almacén
- `movements` - Historial de movimientos
- `stock_reservations` - Reservas de stock
- `suppliers` - Proveedores
- `product_supplier` - Relación productos-proveedores
- `purchase_orders` - Órdenes de compra
- `purchase_order_items` - Items de órdenes

### Relaciones

```
Products ←→ Categories (N:1)
Products ←→ Suppliers (N:N) → product_supplier
Products ←→ Warehouses → Inventories (N:1)
Products → Movements (1:N)
PurchaseOrders → Suppliers (N:1)
PurchaseOrders → PurchaseOrderItems (1:N)
PurchaseOrders → Movements (1:N) [al recibir]
```

## Rutas Web

| URL | Descripción |
|-----|-------------|
| `/` | Dashboard |
| `/productos` | Gestión de productos |
| `/almacenes` | Gestión de almacenes |
| `/inventario` | Control de stock |
| `/inventario/agregar-stock` | Agregar stock |
| `/inventario/remover-stock` | Remover stock |
| `/inventario/ajustar-stock` | Ajustar stock |
| `/inventario/transferir` | Transferir entre almacenes |
| `/inventario/stock-bajo` | Ver stock bajo |
| `/movimientos` | Ver movimientos |
| `/categorias` | Gestión de categorías |
| `/proveedores` | Gestión de proveedores |
| `/ordenes-compra` | Órdenes de compra |
| `/usuarios` | Gestión de usuarios |

## Comandos Artisan útiles

```bash
# Limpiar caché
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Regenerar seeders
php artisan db:seed

# Listar rutas
php artisan route:list

# Ver permisos
php artisan permission:cache-reset
```

## Paquetes instalados

- [Laravel Framework](https://laravel.com) - Framework principal
- [Spatie Permission](https://spatie.be/docs/laravel-permission) - RBAC
- [Maatwebsite Excel](https://docs.laravel-excel.com) - Exportación Excel

## Licencia

Este proyecto es de uso libre para fines educativos y comerciales.
