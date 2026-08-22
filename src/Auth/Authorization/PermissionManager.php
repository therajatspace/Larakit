<?php

namespace Therajatspace\Larakit\Auth\Authorization;

use InvalidArgumentException;
use Spatie\Permission\Models\Permission;

class PermissionManager
{
    public function create(
        string $name,
        ?string $guard = null
    ): Permission {
        $name = $this->normalizeName($name);
        $guard = $guard ?? $this->guard();

        $this->validateName($name);

        return Permission::findOrCreate(
            $name,
            $guard
        );
    }

    public function find(
        string $name,
        ?string $guard = null
    ): ?Permission {
        $name = $this->normalizeName($name);
        $guard = $guard ?? $this->guard();

        return Permission::query()
            ->where('name', $name)
            ->where('guard_name', $guard)
            ->first();
    }

    public function exists(
        string $name,
        ?string $guard = null
    ): bool {
        return $this->find($name, $guard) !== null;
    }

    public function delete(
        string $name,
        ?string $guard = null
    ): bool {
        $permission = $this->find(
            $name,
            $guard
        );

        if ($permission === null) {
            return false;
        }

        return (bool) $permission->delete();
    }

    protected function guard(): string
    {
        return config(
            'larakit.auth.guard'
        ) ?: config(
            'auth.defaults.guard',
            'web'
        );
    }

    protected function normalizeName(
        string $name
    ): string {
        return trim($name);
    }

    protected function validateName(
        string $name
    ): void {
        if ($name === '') {
            throw new InvalidArgumentException(
                'Permission name cannot be empty.'
            );
        }

        if (mb_strlen($name) > 255) {
            throw new InvalidArgumentException(
                'Permission name cannot exceed 255 characters.'
            );
        }
    }
}