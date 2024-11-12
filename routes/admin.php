<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\GameController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\SettingController;

Route::middleware(['auth', 'admin'])->prefix('api/admin')->group(function () {
    // Dashboard Statistics
    Route::get('/stats', [DashboardController::class, 'getStats']);

    // User Management
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus']);
    Route::post('/users/{user}/update-balance', [UserController::class, 'updateBalance']);

    // Game Management
    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);
    Route::put('/games/{game}', [GameController::class, 'update']);
    Route::post('/games/{game}/toggle-status', [GameController::class, 'toggleStatus']);

    // Transaction Management
    Route::get('/transactions', [TransactionController::class, 'index']);
    Route::post('/transactions/{transaction}/approve', [TransactionController::class, 'approve']);
    Route::post('/transactions/{transaction}/reject', [TransactionController::class, 'reject']);

    // Settings Management
    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'update']);
});

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