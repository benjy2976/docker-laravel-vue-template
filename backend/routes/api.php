<?php

use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\PermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user()->load('roles', 'permissions');
    });

    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('permissions', PermissionController::class)->middleware('role:admin');
});
