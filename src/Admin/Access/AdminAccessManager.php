<?php

namespace Therajatspace\Larakit\Admin\Access;

use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;
use Therajatspace\Larakit\Admin\Contracts\AdminAccessManagerContract;

class AdminAccessManager implements AdminAccessManagerContract
{
    public function permission(): ?string
    {
        $permission = config(
            'larakit.admin.access.permission'
        );

        if ($permission === null) {
            return null;
        }

        if (!is_string($permission)) {
            throw new InvalidArgumentException(
                'LaraKit admin access permission must be a string or null.'
            );
        }

        $permission = trim($permission);

        return $permission === ''
            ? null
            : $permission;
    }

    public function canAccess(): bool
    {
        $user = auth()->user();

        if (!$user instanceof Authenticatable) {
            return false;
        }

        $permission = $this->permission();

        if ($permission === null) {
            return false;
        }

        if (!method_exists($user, 'can')) {
            return false;
        }

        return (bool) $user->can($permission);
    }
}