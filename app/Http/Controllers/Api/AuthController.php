<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return response()->json([
            'message' => 'User berhasil dibuat',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $data = $request->validate([
    'email' => ['required', 'email'],
    'password' => ['required', 'string'],
    'mode' => ['nullable', 'in:read,edit,delete,manage'],
]);
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $mode = $data['mode'] ?? 'read';

        $abilities = match ($mode) {
            'read'   => ['menus:read'],
            'edit'   => ['menus:read', 'menus:update'],
            'delete' => ['menus:read', 'menus:delete'],
            'manage' => ['menus:read', 'menus:create', 'menus:update', 'menus:delete'],
        };

        $token = $user->createToken("menu-{$mode}", $abilities)->plainTextToken;

        return response()->json([
            'token_type' => 'Bearer',
            'token' => $token,
            'abilities' => $abilities,
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'message' => 'Halo, ' . $request->user()->name,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Berhasil logout']);
    }
}
