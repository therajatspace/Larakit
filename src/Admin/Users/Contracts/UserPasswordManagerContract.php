<?php

namespace Therajatspace\Larakit\Admin\Users\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserPasswordManagerContract
{
    public function setPassword(
        Authenticatable $user,
        string $password,
        ?string $passwordConfirmation = null
    ): void;
}