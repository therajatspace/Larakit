<?php

namespace Therajatspace\Larakit\Admin\Users;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountManagerContract;
use Therajatspace\Larakit\Admin\Users\Contracts\UserManagementManagerContract;
use Therajatspace\Larakit\Admin\Users\Contracts\UserPasswordManagerContract;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;

class UserManagementManager implements UserManagementManagerContract
{
    public function __construct()
    {
    }

    protected function authorization(): UserAuthorizationManagerContract
    {
        return app()->make(
            UserAuthorizationManagerContract::class
        );
    }

    protected function account(): UserAccountManagerContract
    {
        return app()->make(
            UserAccountManagerContract::class
        );
    }

    protected function password(): UserPasswordManagerContract
    {
        return app()->make(
            UserPasswordManagerContract::class
        );
    }

    public function userModel(): string
    {
        $model = config(
            'larakit.auth.user.model'
        );

        if (!is_string($model) || trim($model) === '') {
            throw new InvalidArgumentException(
                'LaraKit authentication user model is not configured.'
            );
        }

        if (!class_exists($model)) {
            throw new InvalidArgumentException(
                "LaraKit authentication user model [{$model}] does not exist."
            );
        }

        if (
            !is_a(
                $model,
                Model::class,
                true
            )
        ) {
            throw new InvalidArgumentException(
                "LaraKit authentication user model [{$model}] "
                . 'must extend Illuminate\Database\Eloquent\Model.'
            );
        }

        if (
            !is_a(
                $model,
                Authenticatable::class,
                true
            )
        ) {
            throw new InvalidArgumentException(
                "LaraKit authentication user model [{$model}] "
                . 'must implement Illuminate\Contracts\Auth\Authenticatable.'
            );
        }

        return $model;
    }

    public function find(
        mixed $id
    ): ?Authenticatable {
        $model = $this->userModel();

        return $model::query()->find($id);
    }

    public function paginate(
        int $perPage = 25
    ): LengthAwarePaginator {
        if ($perPage < 1) {
            throw new InvalidArgumentException(
                'User pagination per-page value must be at least 1.'
            );
        }

        if ($perPage > 100) {
            throw new InvalidArgumentException(
                'User pagination per-page value cannot exceed 100.'
            );
        }

        $model = $this->userModel();

        return $model::query()->paginate(
            $perPage
        );
    }

    public function activate(
        Authenticatable $user
    ): void {
        $this->account()->activate(
            $user
        );
    }

    public function deactivate(
        Authenticatable $user
    ): void {
        $this->account()->deactivate(
            $user
        );
    }

    public function delete(
        Authenticatable $user
    ): void {
        $this->account()->delete(
            $user
        );
    }

    public function setPassword(
        Authenticatable $user,
        string $password,
        ?string $passwordConfirmation = null
    ): void {
        $this->password()->setPassword(
            $user,
            $password,
            $passwordConfirmation
        );
    }

    public function assignRole(
        Authenticatable $user,
        string $role
    ): void {
        $this->authorization()->assignRole(
            $user,
            $role
        );
    }

    public function removeRole(
        Authenticatable $user,
        string $role
    ): void {
        $this->authorization()->removeRole(
            $user,
            $role
        );
    }

    public function syncRoles(
        Authenticatable $user,
        array $roles
    ): void {
        $this->authorization()->syncRoles(
            $user,
            $roles
        );
    }

    public function givePermission(
        Authenticatable $user,
        string $permission
    ): void {
        $this->authorization()->givePermission(
            $user,
            $permission
        );
    }

    public function revokePermission(
        Authenticatable $user,
        string $permission
    ): void {
        $this->authorization()->revokePermission(
            $user,
            $permission
        );
    }

    public function syncPermissions(
        Authenticatable $user,
        array $permissions
    ): void {
        $this->authorization()->syncPermissions(
            $user,
            $permissions
        );
    }
}