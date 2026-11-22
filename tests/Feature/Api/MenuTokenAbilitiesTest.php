<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

it('menolak daftar menu tanpa token (401)', function () {
    $this->getJson('/api/menus')->assertUnauthorized();
});

it('menolak login dengan password salah (422) dan tidak membuat token', function () {
    $user = User::factory()->create(['password' => 'rahasia123']);

    $this->postJson('/api/login', ['email' => $user->email, 'password' => 'salah'])
        ->assertStatus(422);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

it('login tanpa mode memberi token yang hanya bisa membaca', function () {
    $user = User::factory()->create(['password' => 'rahasia123']);

    $token = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'rahasia123'])
        ->assertOk()
        ->json('token');

    $this->withToken($token)->getJson('/api/menus')->assertOk();

    Auth::forgetGuards();

    $this->withToken($token)
        ->postJson('/api/menus', ['name' => 'X', 'category' => 'Y', 'price' => 1000])
        ->assertForbidden();
});

it('token read ditolak saat mengubah menu (403)', function () {
    $owner = User::factory()->create();
    $menu = $owner->menus()->create(['name' => 'A', 'category' => 'B', 'price' => 1000]);
    Sanctum::actingAs($owner, ['menus:read']);

    $this->putJson("/api/menus/{$menu->id}", ['name' => 'Z', 'category' => 'B', 'price' => 1000])
        ->assertForbidden();
});

it('token edit bisa mengubah menu', function () {
    $owner = User::factory()->create();
    $menu = $owner->menus()->create(['name' => 'A', 'category' => 'B', 'price' => 1000]);
    Sanctum::actingAs($owner, ['menus:read', 'menus:update']);

    $this->putJson("/api/menus/{$menu->id}", ['name' => 'Z', 'category' => 'B', 'price' => 1000])
        ->assertOk();
});

it('menolak user lain mengakses menu bukan miliknya (403)', function () {
    $owner = User::factory()->create();
    $menu = $owner->menus()->create(['name' => 'A', 'category' => 'B', 'price' => 1000]);
    Sanctum::actingAs(User::factory()->create(), ['menus:read']);

    $this->getJson("/api/menus/{$menu->id}")->assertForbidden();
    $this->assertModelExists($menu);
});

it('token tidak bisa dipakai lagi setelah logout (401)', function () {
    $user = User::factory()->create(['password' => 'rahasia123']);

    $token = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'rahasia123'])
        ->json('token');

    $this->withToken($token)->postJson('/api/logout')->assertOk();

    Auth::forgetGuards();

    $this->withToken($token)->getJson('/api/menus')->assertUnauthorized();
});
