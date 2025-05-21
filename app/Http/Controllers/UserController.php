<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a list of users with pagination.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::orderBy('name')->simplePaginate(config('app.pagination_per_page'));
    }

    /**
     * Create a user with a temporary password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|string|exists:roles,name',
            'extra_permissions' => 'sometimes|array',
            'extra_permissions.*' => 'exists:permissions,name'
        ]);

        $tempPassword = Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
        ]);

        $user->assignRole($validated['role']);

        // If extra permissions are provided, sync them directly
        if (isset($validated['extra_permissions'])) {
            $user->syncPermissions($validated['extra_permissions']);
        }

        return response()->json([
            'message' => 'Usuario creado correctamente',
            'user' => $user,
            'temporary_password' => $tempPassword
        ], 201);
    }

    /**
     * Return user's data with their roles and permissions.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the user's data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'sometimes|string|exists:roles,name',
            'extra_permissions' => 'sometimes|array',
            'extra_permissions.*' => 'exists:permissions,name'
        ]);

        $user->update($validated);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        // If extra permissions are provided, sync them directly
        if (isset($validated['extra_permissions'])) {
            $user->syncPermissions($validated['extra_permissions']);
        }

        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'user' => $user,
        ]);
    }

    /**
     * Delete a user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'message' => 'Usuario eliminado correctamente'
        ]);
    }
}
