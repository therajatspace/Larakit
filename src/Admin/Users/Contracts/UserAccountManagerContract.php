<?php

namespace Therajatspace\Larakit\Admin\Users\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserAccountManagerContract
{
    public function activate(
        Authenticatable $user
    ): void;

    public function deactivate(
        Authenticatable $user
    ): void;

    public function delete(
        Authenticatable $user
    ): void;
}