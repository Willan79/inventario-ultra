<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\LoginController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\WarehouseController;
use App\Http\Controllers\Web\InventoryController;
use App\Http\Controllers\Web\MovementController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\UserController;
use App\Http\Controllers\Web\ReportController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('web.login');
Route::post('/login', [LoginController::class, 'login'])->name('web.login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('web.logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('web.dashboard');

    Route::prefix('productos')->name('web.productos.')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->name('index')->middleware('permission:products.view');
        Route::get('/crear', [ProductController::class, 'create'])->name('create')->middleware('permission:products.create');
        Route::post('/', [ProductController::class, 'store'])->name('store')->middleware('permission:products.create');
        Route::get('/{id}', [ProductController::class, 'show'])->name('show')->middleware('permission:products.view');
        Route::get('/{id}/editar', [ProductController::class, 'edit'])->name('edit')->middleware('permission:products.update');
        Route::put('/{id}', [ProductController::class, 'update'])->name('update')->middleware('permission:products.update');
        Route::delete('/{id}', [ProductController::class, 'destroy'])->name('destroy')->middleware('permission:products.delete');
        Route::post('/{id}/toggle-active', [ProductController::class, 'toggleActive'])->name('toggle-active')->middleware('permission:products.update');
    });

    Route::prefix('almacenes')->name('web.almacenes.')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index')->middleware('permission:warehouses.view');
        Route::get('/crear', [WarehouseController::class, 'create'])->name('create')->middleware('permission:warehouses.create');
        Route::post('/', [WarehouseController::class, 'store'])->name('store')->middleware('permission:warehouses.create');
        Route::get('/{id}', [WarehouseController::class, 'show'])->name('show')->middleware('permission:warehouses.view');
        Route::get('/{id}/editar', [WarehouseController::class, 'edit'])->name('edit')->middleware('permission:warehouses.update');
        Route::put('/{id}', [WarehouseController::class, 'update'])->name('update')->middleware('permission:warehouses.update');
        Route::delete('/{id}', [WarehouseController::class, 'destroy'])->name('destroy')->middleware('permission:warehouses.delete');
        Route::post('/{id}/toggle-active', [WarehouseController::class, 'toggleActive'])->name('toggle-active')->middleware('permission:warehouses.update');
    });

    Route::prefix('inventario')->name('web.inventario.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index')->middleware('permission:inventory.view');
        Route::get('/stock-bajo', [InventoryController::class, 'lowStock'])->name('low-stock')->middleware('permission:inventory.view');
        Route::get('/agregar-stock', [InventoryController::class, 'addStock'])->name('add-stock')->middleware('permission:inventory.add');
        Route::post('/agregar-stock', [InventoryController::class, 'storeStock'])->name('store-stock')->middleware('permission:inventory.add');
        Route::get('/remover-stock', [InventoryController::class, 'removeStock'])->name('remove-stock')->middleware('permission:inventory.remove');
        Route::post('/remover-stock', [InventoryController::class, 'storeRemoveStock'])->name('store-remove-stock')->middleware('permission:inventory.remove');
        Route::get('/ajustar-stock', [InventoryController::class, 'adjustStock'])->name('adjust-stock')->middleware('permission:inventory.adjust');
        Route::post('/ajustar-stock', [InventoryController::class, 'storeAdjustStock'])->name('store-adjust-stock')->middleware('permission:inventory.adjust');
        Route::get('/transferir', [InventoryController::class, 'transfer'])->name('transfer')->middleware('permission:inventory.transfer');
        Route::post('/transferir', [InventoryController::class, 'storeTransfer'])->name('store-transfer')->middleware('permission:inventory.transfer');
    });

    Route::prefix('movimientos')->name('web.movimientos.')->group(function () {
        Route::get('/', [MovementController::class, 'index'])->name('index')->middleware('permission:movements.view');
    });

    Route::prefix('categorias')->name('web.categorias.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index')->middleware('permission:categories.view');
        Route::get('/crear', [CategoryController::class, 'create'])->name('create')->middleware('permission:categories.create');
        Route::post('/', [CategoryController::class, 'store'])->name('store')->middleware('permission:categories.create');
        Route::get('/{id}/editar', [CategoryController::class, 'edit'])->name('edit')->middleware('permission:categories.update');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update')->middleware('permission:categories.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy')->middleware('permission:categories.delete');
    });

    Route::prefix('usuarios')->name('web.usuarios.')->middleware('role:super')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/crear', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/editar', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('reportes')->name('web.reportes.')->middleware('permission:inventory.view')->group(function () {
        Route::get('/inventario/excel', [ReportController::class, 'inventory'])->name('inventario.excel');
        Route::get('/movimientos/excel', [ReportController::class, 'movements'])->name('movimientos.excel');
        Route::get('/productos/excel', [ReportController::class, 'products'])->name('productos.excel');
        Route::get('/almacenes/excel', [ReportController::class, 'warehouses'])->name('almacenes.excel');
    });
 //});

    Route::prefix('almacenes')->name('web.almacenes.')->group(function () {
        Route::get('/', [WarehouseController::class, 'index'])->name('index')->middleware('permission:warehouses.view');
        Route::get('/crear', [WarehouseController::class, 'create'])->name('create')->middleware('permission:warehouses.create');
        Route::post('/', [WarehouseController::class, 'store'])->name('store')->middleware('permission:warehouses.create');
        Route::get('/{id}', [WarehouseController::class, 'show'])->name('show')->middleware('permission:warehouses.view');
        Route::get('/{id}/editar', [WarehouseController::class, 'edit'])->name('edit')->middleware('permission:warehouses.update');
        Route::put('/{id}', [WarehouseController::class, 'update'])->name('update')->middleware('permission:warehouses.update');
        Route::delete('/{id}', [WarehouseController::class, 'destroy'])->name('destroy')->middleware('permission:warehouses.delete');
        Route::post('/{id}/toggle-active', [WarehouseController::class, 'toggleActive'])->name('toggle-active')->middleware('permission:warehouses.update');
    });

    Route::prefix('inventario')->name('web.inventario.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index')->middleware('permission:inventory.view');
        Route::get('/stock-bajo', [InventoryController::class, 'lowStock'])->name('low-stock')->middleware('permission:inventory.view');
        Route::get('/agregar-stock', [InventoryController::class, 'addStock'])->name('add-stock')->middleware('permission:inventory.add');
        Route::post('/agregar-stock', [InventoryController::class, 'storeStock'])->name('store-stock')->middleware('permission:inventory.add');
        Route::get('/remover-stock', [InventoryController::class, 'removeStock'])->name('remove-stock')->middleware('permission:inventory.remove');
        Route::post('/remover-stock', [InventoryController::class, 'storeRemoveStock'])->name('store-remove-stock')->middleware('permission:inventory.remove');
        Route::get('/ajustar-stock', [InventoryController::class, 'adjustStock'])->name('adjust-stock')->middleware('permission:inventory.adjust');
        Route::post('/ajustar-stock', [InventoryController::class, 'storeAdjustStock'])->name('store-adjust-stock')->middleware('permission:inventory.adjust');
        Route::get('/transferir', [InventoryController::class, 'transfer'])->name('transfer')->middleware('permission:inventory.transfer');
        Route::post('/transferir', [InventoryController::class, 'storeTransfer'])->name('store-transfer')->middleware('permission:inventory.transfer');
    });

    Route::prefix('movimientos')->name('web.movimientos.')->group(function () {
        Route::get('/', [MovementController::class, 'index'])->name('index')->middleware('permission:movements.view');
    });

    Route::prefix('categorias')->name('web.categorias.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index')->middleware('permission:categories.view');
        Route::get('/crear', [CategoryController::class, 'create'])->name('create')->middleware('permission:categories.create');
        Route::post('/', [CategoryController::class, 'store'])->name('store')->middleware('permission:categories.create');
        Route::get('/{id}/editar', [CategoryController::class, 'edit'])->name('edit')->middleware('permission:categories.update');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('update')->middleware('permission:categories.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy')->middleware('permission:categories.delete');
    });

    Route::prefix('usuarios')->name('web.usuarios.')->middleware('role:super')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/crear', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{id}/editar', [UserController::class, 'edit'])->name('edit');
        Route::put('/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
    });
});
