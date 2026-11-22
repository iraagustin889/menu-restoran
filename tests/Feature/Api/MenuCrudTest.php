<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(LazilyRefreshDatabase::class);

it('membuat menu dan menyimpannya', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['menus:create']);
    $payload = ['name' => 'Nasi Goreng', 'category' => 'Makanan Utama', 'price' => 25000];

    $this->postJson('/api/menus', $payload)->assertSuccessful();

    $this->assertDatabaseHas('menus', [
        ...$payload,
        'user_id' => $user->id,
    ]);
});

it('menampilkan daftar menu', function () {
    $owner = User::factory()->create();
    $owner->menus()->create(['name' => 'Soto', 'category' => 'Sup', 'price' => 15000]);
    Sanctum::actingAs($owner, ['menus:read']);

    $this->getJson('/api/menus')
        ->assertOk()
        ->assertJsonFragment(['name' => 'Soto']);
});

it('menampilkan detail menu milik sendiri', function () {
    $owner = User::factory()->create();
    $menu = $owner->menus()->create(['name' => 'Soto', 'category' => 'Sup', 'price' => 15000]);
    Sanctum::actingAs($owner, ['menus:read']);

    $this->getJson("/api/menus/{$menu->id}")
        ->assertOk()
        ->assertJsonFragment(['id' => $menu->id]);
});

it('mengubah menu milik sendiri', function () {
    $owner = User::factory()->create();
    $menu = $owner->menus()->create(['name' => 'Lama', 'category' => 'Sup', 'price' => 10000]);
    Sanctum::actingAs($owner, ['menus:update']);
    $payload = ['name' => 'Baru', 'category' => 'Sup', 'price' => 12000];

    $this->putJson("/api/menus/{$menu->id}", $payload)->assertOk();

    $this->assertDatabaseHas('menus', ['id' => $menu->id, 'user_id' => $owner->id, ...$payload]);
});

it('menghapus menu milik sendiri', function () {
    $owner = User::factory()->create();
    $menu = $owner->menus()->create(['name' => 'Hapus', 'category' => 'Sup', 'price' => 10000]);
    Sanctum::actingAs($owner, ['menus:delete']);

    $this->deleteJson("/api/menus/{$menu->id}")->assertOk();

    $this->assertModelMissing($menu);
});

it('mengembalikan 404 jika menu tidak ada', function () {
    Sanctum::actingAs(User::factory()->create(), ['menus:read']);

    $this->getJson('/api/menus/99999')->assertNotFound();
});
