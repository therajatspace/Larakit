<?php

namespace Therajatspace\Larakit\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface DelegationManagerContract
{
    public function ensureCanAssignRole(
        Authenticatable $actor,
        string $role
    ): void;

    public function ensureCanAssignPermission(
        Authenticatable $actor,
        string $permission
    ): void;
}