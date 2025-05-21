<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

/*********************
 *** Public routes ***
 ********************/

Route::post('/login', [AuthController::class, 'login']);

/************************
 *** Protected routes ***
 ***********************/

// User Management Routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    /*******************
     *** User Routes ***
     *******************/

    Route::apiResource('users', UserController::class)
        ->middleware([
            'index' => 'permission:list-users',
            'store' => 'permission:create-users',
            'update' => 'permission:edit-users',
            'destroy' => 'permission:delete-users',
            'show' => 'permission:show-user'
        ]);

    /*******************
     *** Role Routes ***
     *******************/

    Route::apiResource('roles', RoleController::class)
        ->middleware([
            'index' => 'permission:list-roles',
            'store' => 'permission:create-roles',
            'update' => 'permission:edit-roles',
            'destroy' => 'permission:delete-roles',
            'show' => 'permission:show-role'
        ]);

    Route::get('roles-and-permissions', [RoleController::class, 'rolesAndPermissions'])
        ->middleware(['permission:list-roles', 'permission:list-permissions']);

    /**************************
     *** Permission Routes ***
     **************************/

    Route::apiResource('permissions', PermissionController::class)
        ->middleware([
            'index' => 'permission:list-permissions',
            'store' => 'permission:create-permissions',
            'update' => 'permission:edit-permissions',
            'destroy' => 'permission:delete-permissions',
            'show' => 'permission:show-permission'
        ]);

    /**********************
     *** Invoice Routes ***
     **********************/

    Route::apiResource('invoices', InvoiceController::class)
        ->middleware([
            'index' => 'permission:list-invoices',
            'store' => 'permission:create-invoices',
            'show' => 'permission:show-invoice'
        ]);
});
