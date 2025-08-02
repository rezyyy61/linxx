<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'manage users',
            'manage posts',
            'manage reports',
            'manage comments',
            'view dashboard',
            'edit site settings',
            'send notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $moderator = Role::firstOrCreate(['name' => 'moderator']);
        $editor = Role::firstOrCreate(['name' => 'editor']);

        $superAdmin->syncPermissions(Permission::all());

        $admin->syncPermissions([
            'manage users',
            'manage posts',
            'manage comments',
            'view dashboard',
        ]);

        $moderator->syncPermissions([
            'manage posts',
            'manage reports',
        ]);

        $editor->syncPermissions([
            'manage posts',
        ]);
    }
}
