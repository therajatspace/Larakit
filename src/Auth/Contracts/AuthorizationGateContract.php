<?php

namespace Therajatspace\Larakit\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface AuthorizationGateContract
{
    public function canManageUsers(
        ?Authenticatable $actor = null
    ): bool;

    public function canManageRoles(
        ?Authenticatable $actor = null
    ): bool;

    public function canManagePermissions(
        ?Authenticatable $actor = null
    ): bool;

    public function canAssignRoles(
        ?Authenticatable $actor = null
    ): bool;

    public function canAssignPermissions(
        ?Authenticatable $actor = null
    ): bool;
}