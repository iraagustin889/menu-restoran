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

    // Token harus punya izin menus:read
    Route::middleware('abilities:menus:read')->group(function () {
        Route::get('/menus', [MenuController::class, 'index']);       // Browse
        Route::get('/menus/{id}', [MenuController::class, 'show']);   // Read
    });

    // Token harus punya izin menus:create
    Route::middleware('abilities:menus:create')->group(function () {
        Route::post('/menus', [MenuController::class, 'store']);      // Add
    });

    // Token harus punya izin menus:update
    Route::middleware('abilities:menus:update')->group(function () {
        Route::put('/menus/{id}', [MenuController::class, 'update']);   // Edit
        Route::patch('/menus/{id}', [MenuController::class, 'update']);
    });

    // Token harus punya izin menus:delete
    Route::middleware('abilities:menus:delete')->group(function () {
        Route::delete('/menus/{id}', [MenuController::class, 'destroy']); // Delete
    });
});
