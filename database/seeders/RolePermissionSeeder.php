<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Products
            'products.view',
            'products.create',
            'products.update',
            'products.delete',

            // Warehouses
            'warehouses.view',
            'warehouses.create',
            'warehouses.update',
            'warehouses.delete',

            // Categories
            'categories.view',
            'categories.create',
            'categories.update',
            'categories.delete',

            // Inventory
            'inventory.view',
            'inventory.add',
            'inventory.remove',
            'inventory.adjust',
            'inventory.transfer',

            // Movements
            'movements.view',

            // Suppliers
            'suppliers.view',
            'suppliers.create',
            'suppliers.update',
            'suppliers.delete',

            // Users (admin only)
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // SUPER - Todos los permisos
        $super = Role::firstOrCreate(['name' => 'super']);
        $super->syncPermissions(Permission::all());

        // ADMIN - Todo excepto usuarios
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions(Permission::whereNotIn('name', [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
        ])->get());

        // USER - Solo lectura
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->syncPermissions([
            'products.view',
            'warehouses.view',
            'categories.view',
            'suppliers.view',
            'inventory.view',
            'movements.view',
        ]);
    }
}
