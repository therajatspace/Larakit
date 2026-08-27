<?php

namespace Therajatspace\Larakit\Admin\Users\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserAccountDriverContract
{
    public function activate(
        Authenticatable $user
    ): void;

    public function deactivate(
        Authenticatable $user
    ): void;

    public function isActive(
        Authenticatable $user
    ): bool;

    public function delete(
        Authenticatable $user
    ): void;
}