<?php

namespace Therajatspace\Larakit\Auth\Authorization;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Therajatspace\Larakit\Auth\Contracts\DelegationConfigContract;

class DelegationManager
{
    public function __construct(
        protected DelegationConfigContract $config,
    ) {
    }

    public function ensureCanAssignRole(
        Authenticatable $actor,
        string $role
    ): void {
        if (! $this->config->enabled()) {
            throw new AuthorizationException(
                'Authorization delegation is disabled.'
            );
        }

        if (! $actor->can(
            AuthorizationPermissions::ROLES_ASSIGN
        )) {
            throw new AuthorizationException(
                'You are not authorized to assign roles.'
            );
        }

        $role = trim($role);

        if ($role === '') {
            throw new AuthorizationException(
                'The role to assign cannot be empty.'
            );
        }

        if ($actor->hasRole($role)) {
            return;
        }

        foreach ($actor->getRoleNames() as $sourceRole) {
            if (
                in_array(
                    $role,
                    $this->config->roleTargets($sourceRole),
                    true
                )
            ) {
                return;
            }
        }

        throw new AuthorizationException(
            'You are not authorized to delegate this role.'
        );
    }

    public function ensureCanAssignPermission(
        Authenticatable $actor,
        string $permission
    ): void {
        if (! $this->config->enabled()) {
            throw new AuthorizationException(
                'Authorization delegation is disabled.'
            );
        }

        if (! $actor->can(
            AuthorizationPermissions::PERMISSIONS_ASSIGN
        )) {
            throw new AuthorizationException(
                'You are not authorized to assign permissions.'
            );
        }

        $permission = trim($permission);

        if ($permission === '') {
            throw new AuthorizationException(
                'The permission to assign cannot be empty.'
            );
        }

        if ($actor->can($permission)) {
            return;
        }

        foreach ($actor->getRoleNames() as $sourceRole) {
            if (
                in_array(
                    $permission,
                    $this->config->permissionTargets($sourceRole),
                    true
                )
            ) {
                return;
            }
        }

        throw new AuthorizationException(
            'You are not authorized to delegate this permission.'
        );
    }
}