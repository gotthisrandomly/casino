<?php

use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\GameConfigurationController;
use Illuminate\Support\Facades\Route;

// User Management Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserManagementController::class, 'index']);
    Route::post('/', [UserManagementController::class, 'store']);
    Route::get('/{user}', [UserManagementController::class, 'show']);
    Route::put('/{user}', [UserManagementController::class, 'update']);
    Route::patch('/{user}/status', [UserManagementController::class, 'updateStatus']);
    Route::post('/{user}/reset-password', [UserManagementController::class, 'resetPassword']);
    Route::get('/export', [UserManagementController::class, 'export']);
});

// Game Configuration Routes
Route::prefix('games')->group(function () {
    Route::get('/', [GameConfigurationController::class, 'index']);
    Route::post('/', [GameConfigurationController::class, 'store']);
    Route::put('/{game}', [GameConfigurationController::class, 'update']);
    Route::patch('/{game}/status', [GameConfigurationController::class, 'updateStatus']);
});

// Game Categories Routes
Route::prefix('game-categories')->group(function () {
    Route::get('/', [GameConfigurationController::class, 'categories']);
    Route::post('/', [GameConfigurationController::class, 'storeCategory']);
    Route::put('/{category}', [GameConfigurationController::class, 'updateCategory']);
    Route::delete('/{category}', [GameConfigurationController::class, 'deleteCategory']);
});