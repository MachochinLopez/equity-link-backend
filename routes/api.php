<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
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
});
