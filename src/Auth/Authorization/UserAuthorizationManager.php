<?php

namespace Therajatspace\Larakit\Auth\Authorization;

use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;
use Spatie\Permission\Traits\HasRoles;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;

class UserAuthorizationManager implements UserAuthorizationManagerContract
{
    public function assignRole(
        Authenticatable $user,
        string $role
    ): void {
        $this->ensureSupportedUser($user);

        $role = trim($role);

        if ($role === '') {
            throw new InvalidArgumentException(
                'Role name cannot be empty.'
            );
        }

        $user->assignRole($role);
    }

    public function removeRole(
        Authenticatable $user,
        string $role
    ): void {
        $this->ensureSupportedUser($user);

        $role = trim($role);

        if ($role === '') {
            throw new InvalidArgumentException(
                'Role name cannot be empty.'
            );
        }

        $user->removeRole($role);
    }

    public function syncRoles(
        Authenticatable $user,
        array $roles
    ): void {
        $this->ensureSupportedUser($user);

        $roles = $this->normalizeList($roles);

        $user->syncRoles($roles);
    }

    public function hasRole(
        Authenticatable $user,
        string $role
    ): bool {
        $this->ensureSupportedUser($user);

        return $user->hasRole(
            trim($role)
        );
    }

    public function givePermission(
        Authenticatable $user,
        string $permission
    ): void {
        $this->ensureSupportedUser($user);

        $permission = trim($permission);

        if ($permission === '') {
            throw new InvalidArgumentException(
                'Permission name cannot be empty.'
            );
        }

        $user->givePermissionTo($permission);
    }

    public function revokePermission(
        Authenticatable $user,
        string $permission
    ): void {
        $this->ensureSupportedUser($user);

        $permission = trim($permission);

        if ($permission === '') {
            throw new InvalidArgumentException(
                'Permission name cannot be empty.'
            );
        }

        $user->revokePermissionTo($permission);
    }

    public function syncPermissions(
        Authenticatable $user,
        array $permissions
    ): void {
        $this->ensureSupportedUser($user);

        $permissions = $this->normalizeList(
            $permissions
        );

        $user->syncPermissions($permissions);
    }

    public function hasPermission(
        Authenticatable $user,
        string $permission
    ): bool {
        $this->ensureSupportedUser($user);

        return $user->hasPermissionTo(
            trim($permission)
        );
    }

    protected function normalizeList(
        array $values
    ): array {
        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        static fn ($value) => trim((string) $value),
                        $values
                    ),
                    static fn ($value) => $value !== ''
                )
            )
        );
    }

    protected function ensureSupportedUser(
        Authenticatable $user
    ): void {
        $traits = class_uses_recursive(
            $user
        );

        if (!in_array(
            HasRoles::class,
            $traits,
            true
        )) {
            throw new InvalidArgumentException(
                'The authenticated user model must use Spatie\\Permission\\Traits\\HasRoles.'
            );
        }
    }
}