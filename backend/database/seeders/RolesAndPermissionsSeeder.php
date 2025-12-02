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
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $admin = Role::where('name', 'admin')->first();
        $user = Role::where('name', 'user')->first();

        if ($admin) {
            $admin->syncPermissions($permissions);
        }

        if ($user) {
            $user->syncPermissions(['projects.view']);
        }
    }
}
