<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Traits\EnsureOwnerTrait;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    use EnsureOwnerTrait;

    // Browse - GET /api/menus
    public function index()
    {
        $menus = Menu::all();
        return response()->json($menus);
    }

    // Read - GET /api/menus/{id}
    public function show(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        $this->ensureOwner($request, $menu);

        return response()->json($menu);
    }

    // Add - POST /api/menus
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $menu = Menu::create($validated);

        return response()->json($menu, 201);
    }

    // Edit - PUT /api/menus/{id}
    public function update(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        $this->ensureOwner($request, $menu);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric',
            'description' => 'nullable|string',
        ]);

        $menu->update($validated);

        return response()->json($menu);
    }

    // Delete - DELETE /api/menus/{id}
    public function destroy(Request $request, $id)
    {
        $menu = Menu::find($id);

        if (!$menu) {
            return response()->json(['message' => 'Menu tidak ditemukan'], 404);
        }

        $this->ensureOwner($request, $menu);

        $menu->delete();

        return response()->json(['message' => 'Menu berhasil dihapus']);
    }
}
