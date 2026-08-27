<?php

namespace Therajatspace\Larakit\Admin\Users\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserManagementManagerContract
{
    public function userModel(): string;

    public function find(
        mixed $id
    ): ?Authenticatable;

    public function paginate(
        int $perPage = 25
    ): LengthAwarePaginator;

    public function activate(
        Authenticatable $user
    ): void;

    public function deactivate(
        Authenticatable $user
    ): void;

    public function delete(
        Authenticatable $user
    ): void;

    public function setPassword(
        Authenticatable $user,
        string $password,
        ?string $passwordConfirmation = null
    ): void;

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
}