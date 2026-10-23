<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\TaskController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:api')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::middleware(['auth:api', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/users', [
            AdminUserController::class,
            'index'
        ]);
    });

Route::middleware('auth:api')->group(function () {
    Route::get('/tasks', [
        TaskController::class, 'index'
    ]);

    Route::post('/tasks', [
        TaskController::class, 'store'
    ]);

    Route::patch('/tasks/{task}/status', [
        TaskController::class, 'updateStatus'
    ]);

    Route::get('/tasks/{task}', [
        TaskController::class, 'show'
    ]);

    Route::match(['put', 'patch'], '/tasks/{task}', [
        TaskController::class, 'update'
    ]);

    Route::delete('/tasks/{task}', [
        TaskController::class, 'destroy'
    ]);

    Route::get('/task-options', [
        TaskController::class,
        'options',
    ]);
});