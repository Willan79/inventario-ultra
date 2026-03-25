# 🏢 Sistema de Inventario - Guía de Implementación

## 📋 Resumen del Proyecto

Sistema de inventario empresarial con:
- **Stack:** Laravel 11 + Bootstrap 5 + MySQL
- **Arquitectura:** Clean Architecture (Domain, Application, Infrastructure)
- **Autenticación:** Session (Web)
- **Autorización:** Spatie Permissions (RBAC)

---

## 🚀 Iniciar Proyecto desde Cero

```bash
# 1. Crear proyecto Laravel
composer create-project laravel/laravel inventario_ultra
cd inventario_ultra

# 2. Instalar dependencias principales
composer require spatie/laravel-permission
composer require maatwebsite/excel

# 3. Configurar base de datos (.env)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventario_ultra
DB_USERNAME=root
DB_PASSWORD=

# 4. Configurar aplicación (.env)
APP_NAME="Inventario Ultra"
APP_LOCALE=es
APP_TIMEZONE=America/Bogota
```

---

## 📁 Estructura de Carpetas

```
app/
├── Application/           # Lógica de aplicación
│   ├── Services/          # Servicios (ProductService, InventoryService, etc.)
│   └── DTOs/              # Data Transfer Objects
├── Domain/                 # Núcleo del negocio
│   ├── Entities/          # Entidades (Product, Warehouse, Inventory, Movement)
│   ├── ValueObjects/     # Objetos de valor (SKU, Quantity)
│   ├── Repositories/     # Interfaces de repositorio
│   └── Exceptions/       # Excepciones del dominio
├── Infrastructure/        # Implementaciones técnicas
│   └── Persistence/
│       ├── Eloquent/
│       │   ├── Models/   # Modelos Eloquent
│       │   └── Repositories/
│       └── Repositories/
└── Http/
    ├── Controllers/
    │   ├── Api/V1/      # Controladores API
    │   └── Web/         # Controladores Web
    └── Resources/       # API Resources
```

---

## 🗄️ Migraciones (Tablas Principales)

```bash
# Crear migración
php artisan make:migration create_products_table

# Tablas necesarias:
# - categories (id, uuid, name, description, parent_id, is_active, sort_order)
# - suppliers (id, uuid, name, contact_name, email, phone, address, is_active, notes)
# - products (id, uuid, sku, name, description, category_id, unit_of_measure, barcode, is_active, min_stock_level, max_stock_level, cost_method)
# - product_supplier (id, product_id, supplier_id, supplier_sku, cost_price, is_preferred) - relación muchos a muchos
# - warehouses (id, uuid, code, name, location, is_active, manager_id)
# - inventories (id, product_id, warehouse_id, quantity_available, quantity_reserved, quantity_on_order, average_cost, last_movement_at)
# - movements (id, uuid, product_id, warehouse_id, movement_type, quantity, previous_quantity, new_quantity, unit_cost, total_cost, reference_type, reference_id, notes, created_by)
# - stock_reservations (id, product_id, warehouse_id, reservation_type, reference_id, quantity, expires_at, status)

php artisan migrate
```

---

## 🔐 Autenticación y Permisos

```bash
# Publicar config de Spatie
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Ejecutar migrate de permisos
php artisan migrate

# Crear Roles en seeder:
# - super (acceso total)
# - admin (sin gestión de usuarios)
# - user (solo lectura)
```

### Permisos recomendados:
```
products.view, products.create, products.edit, products.delete
warehouses.view, warehouses.create, warehouses.edit, warehouses.delete
inventory.view, inventory.add, inventory.remove, inventory.adjust, inventory.transfer
movements.view
categories.view, categories.create, categories.edit, categories.delete
```

---

## ⚙️ Configuraciones Esenciales

### 1. auth.php (Session)
```php
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
]
```

### 2. bootstrap/app.php
```php
->withMiddleware(function ($middleware) {
    $middleware->redirectGuestsTo(fn() => route('login'));
})
->withExceptions(function ($exceptions) {
    // Configurar vistas de errores
})
```

---

## 📦 Paquetes Recomendados

| Paquete | Uso | Comando |
|---------|-----|---------|
| spatie/laravel-permission | RBAC | `composer require spatie/laravel-permission` |
| maatwebsite/excel | Exportar Excel | `composer require maatwebsite/excel` |

---

## 🎨 Vistas Blade

### Estructura recomendada:
```
resources/views/
├── layouts/
│   └── app.blade.php      # Layout principal con sidebar
├── dashboard/
│   └── index.blade.php
├── products/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── warehouses/
├── inventory/
├── movements/
├── categories/
└── users/
```

### Componentes Bootstrap útiles:
- Navbar con dropdown de usuario
- Sidebar con @can directives
- Tablas con paginación
- Forms con validación
- Alerts para mensajes (temporales)

---


## ✅ Checklist de Implementación

### Fase 1: Base
- [ ] Instalación de Laravel
- [ ] Configuración de base de datos
- [ ] Migraciones completas
- [ ] Modelos Eloquent

### Fase 2: Autenticación
- [ ] Sanctum configurado
- [ ] Spatie Permissions instalado
- [ ] Roles creados (super, admin, user)
- [ ] Login web funcional

### Fase 3: Dominio
- [ ] Entities (Product, Warehouse, Inventory, Movement)
- [ ] Value Objects (SKU)
- [ ] Repository Interfaces
- [ ] Application Services

### Fase 4: Web
- [ ] Layout con sidebar
- [ ] Vistas CRUD
- [ ] Filtros y paginación

### Fase 5: Extras
- [ ] Exportación Excel
- [ ] Traducciones al español

---

## 🔧 Comandos Útiles

```bash
# Limpiar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Crear entidad
php artisan make:entity Product

# Crear controlador
php artisan make:controller Api/V1/ProductController

# Crear servicio
php artisan make:service ProductService

# Publicar traducciones
php artisan vendor:publish --lang
```

---

## 📌 Decisiones Técnicas

1. **Rutas en español** `productos`, `almacenes`, `movimientos`
2. **SKU auto-generado** si no se proporciona
3. **Stock bajo** usa `min_stock_level` (no reorder_point)
4. **Transferencias** crean dos movimientos (salida/entrada)
5. **Soft Deletes** para auditoría

---

## ⚠️ Problemas Comunes y Soluciones

| Problema | Solución |
|----------|----------|
| Rutas conflictivas | Usar nombres en español para web |
| Type casting en forms | Convertir explicitamente `(bool)`, `(int)` |
| DTO vs Model properties | Views usan camelCase del DTO |
| Date parsing errors | No usar `$attributes` en modelos |

**Prioridad decisiones:** Seguridad > Correctitud > Performance > Features
