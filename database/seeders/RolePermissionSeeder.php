<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'manage_categories',
            'manage_users',
            'manage_settings',
            'view_reports',
            'take_interviews',
            'view_results',
        ];

        // Create permissions
        foreach ($permissions as $permissionName) {
            Permission::create(['name' => $permissionName]);
        }

        // Create Admin role and assign all permissions
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Create User role and assign specific permissions
        $userRole = Role::create(['name' => 'User']);
        $userRole->givePermissionTo([
            'take_interviews',
            'view_results',
        ]);
    }
}
