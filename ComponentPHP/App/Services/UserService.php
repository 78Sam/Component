<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;

final class UserService
{
    public function getUser(): ?User
    {
        $userString = $_SESSION['user'] ?? null;
        if ($userString === null) {
            return null;
        }

        return unserialize($userString);
    }
}
