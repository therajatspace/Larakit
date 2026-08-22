<?php

namespace Therajatspace\Larakit\Auth\Authorization;

use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;

class AuthorizationGate
{
    public function canManageUsers(
        ?Authenticatable $actor = null
    ): bool {
        return $this->can(
            $actor,
            AuthorizationPermissions::USERS_MANAGE
        );
    }

    public function canManageRoles(
        ?Authenticatable $actor = null
    ): bool {
        return $this->can(
            $actor,
            AuthorizationPermissions::ROLES_MANAGE
        );
    }

    public function canManagePermissions(
        ?Authenticatable $actor = null
    ): bool {
        return $this->can(
            $actor,
            AuthorizationPermissions::PERMISSIONS_MANAGE
        );
    }

    public function canAssignRoles(
        ?Authenticatable $actor = null
    ): bool {
        return $this->can(
            $actor,
            AuthorizationPermissions::ROLES_ASSIGN
        );
    }

    public function canAssignPermissions(
        ?Authenticatable $actor = null
    ): bool {
        return $this->can(
            $actor,
            AuthorizationPermissions::PERMISSIONS_ASSIGN
        );
    }

    protected function can(
        ?Authenticatable $actor,
        string $permission
    ): bool {
        $actor ??= auth()->user();

        if ($actor === null) {
            return false;
        }

        if (!method_exists($actor, 'can')) {
            throw new InvalidArgumentException(
                'The authorization actor must support Laravel authorization checks.'
            );
        }

        return $actor->can($permission);
    }
}