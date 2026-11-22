<?php

use App\Support\MenuOwnership;

test('memeriksa siapa pemilik menu', function (int $userId, ?int $ownerId, bool $expected) {
    expect(MenuOwnership::allows($userId, $ownerId))->toBe($expected);
})->with([
    'pemilik'       => [7, 7, true],
    'pengguna lain' => [8, 7, false],
    'tanpa owner'   => [7, null, false],
]);
