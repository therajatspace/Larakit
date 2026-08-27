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

        $role = trim($role);

        if ($role === '') {
            return false;
        }

        return $user->hasRole($role);
    }

    public function supportsRoles(
        Authenticatable $user
    ): bool {
        return $this->hasRolesTrait($user);
    }

    public function roles(
        Authenticatable $user
    ): array {
        $this->ensureSupportedUser($user);

        return $user
            ->getRoleNames()
            ->map(
                static fn ($role): string => trim(
                    (string) $role
                )
            )
            ->filter(
                static fn (string $role): bool => $role !== ''
            )
            ->unique()
            ->values()
            ->all();
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

        $permission = trim($permission);

        if ($permission === '') {
            return false;
        }

        return $user->hasPermissionTo(
            $permission
        );
    }

    public function supportsPermissions(
        Authenticatable $user
    ): bool {
        return $this->hasRolesTrait($user);
    }

    public function permissions(
        Authenticatable $user
    ): array {
        $this->ensureSupportedUser($user);

        /*
         * getAllPermissions() is intentionally used instead of
         * getDirectPermissions().
         *
         * The Admin Panel needs to know what the user can actually
         * do, including permissions inherited from assigned roles.
         */
        return $user
            ->getAllPermissions()
            ->pluck('name')
            ->map(
                static fn ($permission): string => trim(
                    (string) $permission
                )
            )
            ->filter(
                static fn (string $permission): bool =>
                    $permission !== ''
            )
            ->unique()
            ->values()
            ->all();
    }

    protected function normalizeList(
        array $values
    ): array {
        return array_values(
            array_unique(
                array_filter(
                    array_map(
                        static fn ($value): string => trim(
                            (string) $value
                        ),
                        $values
                    ),
                    static fn ($value): bool =>
                        $value !== ''
                )
            )
        );
    }

    protected function hasRolesTrait(
        Authenticatable $user
    ): bool {
        return in_array(
            HasRoles::class,
            class_uses_recursive($user),
            true
        );
    }

    protected function ensureSupportedUser(
        Authenticatable $user
    ): void {
        if (!$this->hasRolesTrait($user)) {
            throw new InvalidArgumentException(
                'The authenticated user model must use Spatie\\Permission\\Traits\\HasRoles.'
            );
        }
    }
}