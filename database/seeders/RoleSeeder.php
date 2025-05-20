<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createPermissions();

        $adminRole = Role::create(['name' => 'admin']);
        $employeeRole = Role::create(['name' => 'employee']);

        $adminRole->givePermissionTo($this->allPermissions());
        $employeeRole->givePermissionTo($this->commonPermissions());
    }

    /**
     * Get the permissions for the admin role.
     *
     * @return array
     */
    private function adminPermissions()
    {
        $adminPermissions = [
            // User management
            'view-user-management',
            'list-users',
            'create-users',
            'edit-users',
            'delete-users',
            'show-user',

            // Role management
            'add-roles-to-users',
            'remove-roles-from-users',
            'list-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'show-role',

            // Permission management
            'list-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',
            'show-permission',
        ];

        return $adminPermissions;
    }

    /**
     * Get the permissions for the common role.
     *
     * @return array
     */
    private function commonPermissions()
    {
        $commonPermissions = [
            'view-dashboard',
            'view-profile',

            // Invoices
            'view-invoices',
            'list-invoices',
            'upload-invoices',
            'show-invoice',
        ];

        return $commonPermissions;
    }

    /**
     * Get all the permissions.
     *
     * @return array
     */
    private function allPermissions()
    {
        $commonPermissions = $this->commonPermissions();
        $adminPermissions = $this->adminPermissions();

        return array_merge($adminPermissions, $commonPermissions);
    }

    /**
     * Create the permissions.
     */
    private function createPermissions()
    {
        $permissions = $this->allPermissions();

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
