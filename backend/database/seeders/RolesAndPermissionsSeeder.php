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
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
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
                        ],
                    ],
                ],
            ],
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
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
                $permissions,
                $menuPermissionNames
            ));
        }

        if ($user) {
            $user->syncPermissions(['projects.view']);
        }
    }
}
