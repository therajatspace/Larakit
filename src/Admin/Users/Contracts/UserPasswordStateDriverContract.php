<?php

namespace Therajatspace\Larakit\Admin\Users\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserPasswordStateDriverContract
{
    public function forcePasswordChange(
        Authenticatable $user
    ): void;

    public function clearForcedPasswordChange(
        Authenticatable $user
    ): void;

    public function mustChangePassword(
        Authenticatable $user
    ): bool;
}