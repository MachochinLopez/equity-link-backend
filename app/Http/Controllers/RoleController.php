<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Display a list of roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Role::select('id', 'name')->orderBy('name')->simplePaginate(config('app.pagination_per_page'));
    }

    /**
     * Display a list of roles with pagination.
     *
     * @return \Illuminate\Http\Response
     */
    public function rolesAndPermissions()
    {
        $roles = Role::select('roles.id', 'roles.name')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->selectRaw('GROUP_CONCAT(permissions.name) as permissions')
            ->groupBy('roles.id', 'roles.name')
            ->orderBy('roles.name')
            ->simplePaginate(config('app.pagination_per_page'));

        $roles->getCollection()->transform(function ($role) {
            $role->permissions = explode(',', $role->permissions);
            return $role;
        });

        return response()->json(['roles' => $roles]);
    }

    /**
     * Create a new role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);
        $role->syncPermissions($validated['permissions']);

        // Get the role with permissions in the same format as index
        $role = Role::select('roles.id', 'roles.name')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->selectRaw('GROUP_CONCAT(permissions.name) as permissions')
            ->where('roles.id', $role->id)
            ->groupBy('roles.id', 'roles.name')
            ->first();

        $role->permissions = explode(',', $role->permissions);

        return response()->json([
            'message' => 'Role created successfully',
            'role' => $role
        ], 201);
    }

    /**
     * Return role's data.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        $role = Role::select('roles.id', 'roles.name')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->selectRaw('GROUP_CONCAT(permissions.name) as permissions')
            ->where('roles.id', $role->id)
            ->groupBy('roles.id', 'roles.name')
            ->first();

        $role->permissions = explode(',', $role->permissions);

        return response()->json(['role' => $role]);
    }

    /**
     * Update the role's data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        if (isset($validated['name'])) {
            $role->update(['name' => $validated['name']]);
        }

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        // Get the updated role with permissions in the same format
        $role = Role::select('roles.id', 'roles.name')
            ->join('role_has_permissions', 'roles.id', '=', 'role_has_permissions.role_id')
            ->join('permissions', 'role_has_permissions.permission_id', '=', 'permissions.id')
            ->selectRaw('GROUP_CONCAT(permissions.name) as permissions')
            ->where('roles.id', $role->id)
            ->groupBy('roles.id', 'roles.name')
            ->first();

        $role->permissions = explode(',', $role->permissions);

        return response()->json([
            'message' => 'Role updated successfully',
            'role' => $role
        ]);
    }

    /**
     * Remove the specified role.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return response()->json([
            'message' => 'Role deleted successfully'
        ]);
    }
}
