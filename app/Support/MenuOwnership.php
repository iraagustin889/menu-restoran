<?php

namespace App\Support;

class MenuOwnership
{
    public static function allows(int $userId, ?int $ownerId): bool
    {
        return $userId === $ownerId;
    }
}
