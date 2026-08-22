<?php

namespace Therajatspace\Larakit\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Therajatspace\Larakit\Auth\Login\LoginData;

interface LoginServiceContract
{
    public function login(
        LoginData $data
    ): Authenticatable;

    public function logout(): void;
}