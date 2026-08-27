<?php

namespace Therajatspace\Larakit\Auth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface UserAuthorizationManagerContract
{
    public function assignRole(
        Authenticatable $user,
        string $role
    ): void;

    public function removeRole(
        Authenticatable $user,
        string $role
    ): void;

    public function syncRoles(
        Authenticatable $user,
        array $roles
    ): void;

    public function hasRole(
        Authenticatable $user,
        string $role
    ): bool;

    public function supportsRoles(
        Authenticatable $user
    ): bool;

    public function roles(
        Authenticatable $user
    ): array;

    public function givePermission(
        Authenticatable $user,
        string $permission
    ): void;

    public function revokePermission(
        Authenticatable $user,
        string $permission
    ): void;

    public function syncPermissions(
        Authenticatable $user,
        array $permissions
    ): void;

    public function hasPermission(
        Authenticatable $user,
        string $permission
    ): bool;

    public function supportsPermissions(
        Authenticatable $user
    ): bool;

    public function permissions(
        Authenticatable $user
    ): array;
}