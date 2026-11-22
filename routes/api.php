<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

// Bebas diakses tanpa login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Wajib login (bawa token)
    Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    Route::get('/menus', [MenuController::class, 'index']);         // Browse
    Route::get('/menus/{id}', [MenuController::class, 'show']);     // Read
    Route::post('/menus', [MenuController::class, 'store']);        // Add
    Route::put('/menus/{id}', [MenuController::class, 'update']);   // Edit
    Route::patch('/menus/{id}', [MenuController::class, 'update']);
    Route::delete('/menus/{id}', [MenuController::class, 'destroy']); // Delete
});
