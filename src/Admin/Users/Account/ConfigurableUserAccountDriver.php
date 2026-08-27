<?php

namespace Therajatspace\Larakit\Admin\Users\Account;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use RuntimeException;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountDriverContract;

class ConfigurableUserAccountDriver implements UserAccountDriverContract
{
    public function activate(
        Authenticatable $user
    ): void {
        $this->setActiveState(
            $user,
            true
        );
    }

    public function deactivate(
        Authenticatable $user
    ): void {
        $this->setActiveState(
            $user,
            false
        );
    }

    public function isActive(
        Authenticatable $user
    ): bool {
        $attribute = $this->statusAttribute();

        if ($attribute === null) {
            throw new RuntimeException(
                'LaraKit Admin user account status attribute is not configured.'
            );
        }

        if (!$this->supportsAttribute($user, $attribute)) {
            throw new InvalidArgumentException(
                "User model does not support configured account status attribute [{$attribute}]."
            );
        }

        return (bool) $user->getAttribute($attribute);
    }

    public function delete(
        Authenticatable $user
    ): void {
        if (!$user instanceof Model) {
            throw new InvalidArgumentException(
                'The configured user model must extend '
                . 'Illuminate\Database\Eloquent\Model to be deleted by '
                . 'the default account driver.'
            );
        }

        if (!method_exists($user, 'delete')) {
            throw new RuntimeException(
                'The configured user model does not support deletion.'
            );
        }

        $user->delete();
    }

    protected function setActiveState(
        Authenticatable $user,
        bool $active
    ): void {
        $attribute = $this->statusAttribute();

        if ($attribute === null) {
            throw new RuntimeException(
                'LaraKit Admin user account status attribute is not configured.'
            );
        }

        if (!$this->supportsAttribute($user, $attribute)) {
            throw new InvalidArgumentException(
                "User model does not support configured account status attribute [{$attribute}]."
            );
        }

        $user->setAttribute(
            $attribute,
            $active
        );

        if (!$user instanceof Model) {
            throw new InvalidArgumentException(
                'The configured user model must extend '
                . 'Illuminate\Database\Eloquent\Model to persist account status.'
            );
        }

        $user->save();
    }

    protected function statusAttribute(): ?string
    {
        $attribute = config(
            'larakit.admin.users.account.status_attribute'
        );

        if ($attribute === null) {
            return null;
        }

        if (!is_string($attribute)) {
            throw new InvalidArgumentException(
                'LaraKit Admin user account status attribute must be a string or null.'
            );
        }

        $attribute = trim($attribute);

        return $attribute === ''
            ? null
            : $attribute;
    }

    protected function supportsAttribute(
        Authenticatable $user,
        string $attribute
    ): bool {
        if (!$user instanceof Model) {
            return false;
        }

        return array_key_exists(
            $attribute,
            $user->getAttributes()
        ) || array_key_exists(
            $attribute,
            $user->getCasts()
        );
    }
}