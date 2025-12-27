<?php

use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        $user = $request->user()->load('roles.permissions');
        // Exponer permisos efectivos (propios + por rol)
        $user->setRelation('permissions', $user->getAllPermissions());

        return $user;
    });

    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('permissions', PermissionController::class);
    Route::apiResource('roles', RoleController::class);
    Route::put('/user/profile', [ProfileController::class, 'updateProfile']);
    Route::put('/user/password', [ProfileController::class, 'updatePassword']);
});
