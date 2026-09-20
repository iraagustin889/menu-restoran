<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;

Route::get('/menus', [MenuController::class, 'index']);       // Browse
Route::get('/menus/{id}', [MenuController::class, 'show']);   // Read
Route::post('/menus', [MenuController::class, 'store']);      // Add
Route::put('/menus/{id}', [MenuController::class, 'update']); // Edit
Route::patch('/menus/{id}', [MenuController::class, 'update']); 
Route::delete('/menus/{id}', [MenuController::class, 'destroy']); // Delete