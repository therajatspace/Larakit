<?php

namespace Therajatspace\Larakit\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface DelegationServiceContract
{
    public function assignRole(
        Authenticatable $actor,
        Authenticatable $target,
        string $role
    ): void;

    public function assignPermission(
        Authenticatable $actor,
        Authenticatable $target,
        string $permission
    ): void;
}