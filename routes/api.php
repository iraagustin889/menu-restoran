<?php

use App\Http\Controllers\Api\AuthController;
use App\Models\Menu;
use Illuminate\Support\Facades\Route;

// Bebas diakses tanpa login
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Wajib login (bawa token)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // ====== CRUD menu makanan: PASTE PUNYAMU DI SINI ======
    Route::get('/menus', function () {
        return response()->json(['data' => Menu::get()]);
    });

    Route::get('/menu/{id}', function ($id) {
        return response()->json(['data' => Menu::findOrFail($id)]);
    });

    Route::post('/menus', function () {
        $menu = Menu::create([
            'nama' => request()->get('nama'),
            'harga' => request()->get('harga'),
        ]);
        return response()->json(['message' => 'Menu ditambahkan', 'data' => $menu]);
    });

    Route::put('/menu/{id}', function ($id) {
        $menu = Menu::findOrFail($id);
        $menu->update([
            'nama' => request()->get('nama'),
            'harga' => request()->get('harga'),
        ]);
        return response()->json(['message' => 'Menu diubah']);
    });

    Route::delete('/menu/{id}', function ($id) {
        Menu::findOrFail($id)->delete();
        return response()->json(['message' => 'Menu dihapus']);
    });
    // ======================================================
});