<?php

namespace Therajatspace\Larakit\Auth\Authorization;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\DB;
use Therajatspace\Larakit\Auth\Contracts\DelegationServiceContract;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;

class DelegationService implements DelegationServiceContract
{
    public function __construct(
        protected DelegationManager $delegation,
        protected UserAuthorizationManagerContract $authorization,
    ) {
    }

    public function assignRole(
        Authenticatable $actor,
        Authenticatable $target,
        string $role
    ): void {
        $this->ensureDifferentUsers(
            $actor,
            $target
        );

        $role = trim($role);

        if ($role === '') {
            throw new AuthorizationException(
                'The role to assign cannot be empty.'
            );
        }

        $this->delegation->ensureCanAssignRole(
            $actor,
            $role
        );

        DB::transaction(function () use (
            $target,
            $role
        ): void {
            $this->authorization->assignRole(
                $target,
                $role
            );
        });
    }

    public function assignPermission(
        Authenticatable $actor,
        Authenticatable $target,
        string $permission
    ): void {
        $this->ensureDifferentUsers(
            $actor,
            $target
        );

        $permission = trim($permission);

        if ($permission === '') {
            throw new AuthorizationException(
                'The permission to assign cannot be empty.'
            );
        }

        $this->delegation->ensureCanAssignPermission(
            $actor,
            $permission
        );

        DB::transaction(function () use (
            $target,
            $permission
        ): void {
            $this->authorization->givePermission(
                $target,
                $permission
            );
        });
    }

    protected function ensureDifferentUsers(
        Authenticatable $actor,
        Authenticatable $target
    ): void {
        if (
            (string) $actor->getAuthIdentifier() ===
            (string) $target->getAuthIdentifier()
        ) {
            throw new AuthorizationException(
                'You cannot delegate authorization to yourself.'
            );
        }
    }
}