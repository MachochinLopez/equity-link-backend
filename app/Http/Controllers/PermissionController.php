<?php

namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Display a list of permissions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Permission::select('id', 'name')->orderBy('name')
            ->simplePaginate(config('app.pagination_per_page'));
    }

    /**
     * Create a new permission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name'
        ]);

        $permission = Permission::create([
            'name' => $validated['name'],
            'guard_name' => 'web'
        ]);

        return response()->json([
            'message' => 'Permiso creado correctamente',
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name
            ]
        ], 201);
    }

    /**
     * Return permission's data.
     *
     * @param  \Spatie\Permission\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        return $permission;
    }

    /**
     * Update the permission's data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Spatie\Permission\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id
        ]);

        $permission->update([
            'name' => $validated['name'],
            'guard_name' => 'web'
        ]);

        return response()->json([
            'message' => 'Permiso actualizado correctamente',
            'permission' => [
                'id' => $permission->id,
                'name' => $permission->name
            ]
        ]);
    }

    /**
     * Delete the permission.
     *
     * @param  \Spatie\Permission\Models\Permission  $permission
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return response()->json([
            'message' => 'Permiso eliminado correctamente'
        ]);
    }
}
