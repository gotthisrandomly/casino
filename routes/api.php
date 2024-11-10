<?php

use App\Http\Controllers\Api\GameController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::get('games', [GameController::class, 'index']);
Route::get('games/{game}', [GameController::class, 'show']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Game session management
    Route::post('games/{game}/initialize', [GameController::class, 'initialize']);
    Route::post('sessions/{session}/play', [GameController::class, 'play']);
    Route::post('sessions/{session}/end', [GameController::class, 'end']);
});