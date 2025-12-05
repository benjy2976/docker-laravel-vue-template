<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['admin', 'user'];
        $permissions = [
            ['name' => 'projects.view', 'description' => 'Ver proyectos'],
            ['name' => 'projects.create', 'description' => 'Crear proyectos'],
            ['name' => 'projects.edit', 'description' => 'Editar proyectos'],
            ['name' => 'projects.delete', 'description' => 'Eliminar proyectos'],
            ['name' => 'permissions.view', 'description' => 'Ver permisos'],
            ['name' => 'permissions.create', 'description' => 'Crear permisos'],
            ['name' => 'permissions.edit', 'description' => 'Editar permisos'],
            ['name' => 'permissions.delete', 'description' => 'Eliminar permisos'],
            ['name' => 'roles.view', 'description' => 'Ver roles'],
            ['name' => 'roles.create', 'description' => 'Crear roles'],
            ['name' => 'roles.edit', 'description' => 'Editar roles'],
            ['name' => 'roles.delete', 'description' => 'Eliminar roles'],
        ];

        // Menú de Administración y sus entradas
        $menuItems = [
            [
                'name'    => 'menu.administration',
                'data'    => [
                    'is_menu'     => true,
                    'menu_label'  => 'Administración',
                    'menu_path'   => '/admin',
                    'icon'        => 'bi-gear',
                    'sort_order'  => 10,
                    'description' => 'Entrada de menú: Administración',
                ],
                'children' => [
                    [
                        'name' => 'permissions.view',
                        'data' => [
                            'is_menu'     => true,
                            'menu_label'  => 'Permisos',
                            'menu_path'   => '/permissions',
                            'icon'        => 'bi-shield-lock',
                            'sort_order'  => 1,
                            'description' => 'Entrada de menú: Permisos',
                        ],
                    ],
                    [
                        'name' => 'roles.view',
                        'data' => [
                            'is_menu'     => true,
                            'menu_label'  => 'Roles',
                            'menu_path'   => '/roles',
                            'icon'        => 'bi-people',
                            'sort_order'  => 2,
                            'description' => 'Entrada de menú: Roles',
                        ],
                    ],
                ],
            ],
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $permissionNames = [];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['guard_name' => 'web', 'description' => $permission['description']]
            );
            $permissionNames[] = $permission['name'];
        }

        // Crear menú y subentradas (usando la tabla permissions)
        $menuPermissionNames = [];

        foreach ($menuItems as $item) {
            $parent = Permission::updateOrCreate(
                ['name' => $item['name']],
                array_merge(['guard_name' => 'web'], $item['data'])
            );

            $menuPermissionNames[] = $parent->name;

            foreach ($item['children'] as $child) {
                $childModel = Permission::updateOrCreate(
                    ['name' => $child['name']],
                    array_merge(
                        ['guard_name' => 'web', 'parent_id' => $parent->id],
                        $child['data']
                    )
                );

                $menuPermissionNames[] = $childModel->name;
            }
        }

        $admin = Role::where('name', 'admin')->first();
        $user = Role::where('name', 'user')->first();

        if ($admin) {
            $admin->syncPermissions(array_merge(
                $permissionNames,
                $menuPermissionNames
            ));
        }

        if ($user) {
            $user->syncPermissions(['projects.view']);
        }
    }
}
