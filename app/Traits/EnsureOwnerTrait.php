<?php

namespace App\Traits;

use App\Models\Menu;
use App\Support\MenuOwnership;
use Illuminate\Http\Request;

trait EnsureOwnerTrait
{
    private function ensureOwner(Request $request, Menu $menu): void
    {
        abort_unless(
            MenuOwnership::allows($request->user()->id, $menu->user_id),
            403,
            'Menu ini hanya dapat diakses oleh pemiliknya.'
        );
    }
}
