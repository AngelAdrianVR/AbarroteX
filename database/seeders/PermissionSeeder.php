<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;


class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'Punto de venta' => [
                'Punto de venta',
                'Editar precios',
                'Ver dinero en caja',
                'Registrar movimientos de caja',
                'Crear productos expres',
            ],
            'Reportes' => [
                'Reportes',
                'Filtrar por periodo',
            ],
            'Ventas registradas' => [
                'Ventas registradas',
                'Editar ventas',
                'Reembolsar ventas',
                'Registrar abonos',
                'Cancelar ventas en linea',
            ],
            'Gastos' => [
                'Gastos',
                'Crear gastos',
                'Editar gastos',
                'Eliminar gastos',
            ],
            'Productos' => [
                'Productos',
                'Crear productos',
                'Editar productos',
                'Eliminar productos',
                'Exportar productos',
                'Importar productos',
            ],
            'Cotizaciones' => [
                'Cotizaciones',
                'Crear cotizaciones',
                'Editar cotizaciones',
                'Eliminar cotizaciones',
                'Cambiar status cotizaciones',
            ],
            'Renta de productos' => [
                'Renta de productos',
                'Crear rentas',
                'Editar rentas',
                'Eliminar rentas',
                'Crear pagos',
                'Editar pagos',
                'Eliminar pagos',
            ],
            'Servicios' => [
                'Servicios',
                'Crear servicios',
                'Editar servicios',
                'Eliminar servicios',
            ],
            'Clientes' => [
                'Clientes',
                'Crear clientes',
                'Editar clientes',
                'Eliminar clientes',
            ],
            'Caja' => [
                'Caja',
                'Crear cajas',
                'Editar cajas',
                'Eliminar cajas',
                'Registrar movimientos',
                'Ver historial de cortes',
            ],
            'Tienda en línea' => [
                'Tienda en línea',
                'Crear pedidos',
                'Editar pedidos',
                'Eliminar pedidos',
                'Cambiar status de pedidos',
                'Mandar whatsapp',
            ],
            'Configuraciones' => [
                'Configuraciones',
                'Ver configuraciones de punto de venta',
                'Ver usuarios',
                'Crear usuarios',
                'Editar usuarios',
                'Eliminar usuarios',
                'Resetear contraseña de usuarios',
                'Ver configuraciones de impresoras',
            ],
        ];

        $roles = [
            'Cajero',
        ];

        // Crear permisos en base de datos
        foreach ($permissions as $category => $permissions) {
            foreach ($permissions as $name) {
                Permission::create(['name' => $name, 'category' => $category]);
            }
        }

        // Crear rol de super admin y dar todos los persmisos
        $super = Role::create(['name' => 'Administrador']);
        $all_permissions = Permission::all()->pluck('name');
        $super->syncPermissions($all_permissions);

        // Crear roles en base de datos
        foreach ($roles as $name) {
            Role::create(['name' => $name]);
        }

        // asignar rol a usuarios registrados al momento
        $users = User::all();
        $users->each(fn($user) => $user->assignRole($super));
    }
}
