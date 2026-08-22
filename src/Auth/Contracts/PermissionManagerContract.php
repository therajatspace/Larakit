<?php

namespace Therajatspace\Larakit\Auth\Contracts;

use Spatie\Permission\Models\Permission;

interface PermissionManagerContract
{
    public function create(
        string $name,
        ?string $guard = null
    ): Permission;

    public function find(
        string $name,
        ?string $guard = null
    ): ?Permission;

    public function exists(
        string $name,
        ?string $guard = null
    ): bool;

    public function delete(
        string $name,
        ?string $guard = null
    ): bool;
}