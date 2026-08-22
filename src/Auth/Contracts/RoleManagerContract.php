<?php

namespace Therajatspace\Larakit\Auth\Contracts;

use Spatie\Permission\Models\Role;

interface RoleManagerContract
{
    public function create(
        string $name,
        ?string $guard = null
    ): Role;

    public function find(
        string $name,
        ?string $guard = null
    ): ?Role;

    public function exists(
        string $name,
        ?string $guard = null
    ): bool;

    public function delete(
        string $name,
        ?string $guard = null
    ): bool;
}