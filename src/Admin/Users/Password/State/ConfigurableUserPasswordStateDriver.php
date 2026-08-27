<?php

namespace Therajatspace\Larakit\Admin\Users\Password\State;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use RuntimeException;
use Therajatspace\Larakit\Admin\Users\Contracts\UserPasswordStateDriverContract;

class ConfigurableUserPasswordStateDriver implements UserPasswordStateDriverContract
{
    public function forcePasswordChange(
        Authenticatable $user
    ): void {
        $this->setState(
            $user,
            true
        );
    }

    public function clearForcedPasswordChange(
        Authenticatable $user
    ): void {
        $this->setState(
            $user,
            false
        );
    }

    public function mustChangePassword(
        Authenticatable $user
    ): bool {
        $attribute = $this->attribute();

        $this->ensureAttribute(
            $user,
            $attribute
        );

        return (bool) $user->getAttribute(
            $attribute
        );
    }

    protected function setState(
        Authenticatable $user,
        bool $required
    ): void {
        $attribute = $this->attribute();

        $this->ensureAttribute(
            $user,
            $attribute
        );

        $user->setAttribute(
            $attribute,
            $required
        );

        $user->save();
    }

    protected function attribute(): string
    {
        $attribute = config(
            'larakit.admin.users.password.force_change_attribute'
        );

        /*
         * null means that the application has not configured
         * force-password-change support.
         */
        if ($attribute === null) {
            throw new RuntimeException(
                'LaraKit Admin password force-change attribute is not configured.'
            );
        }

        /*
         * An empty string is also treated as "not configured".
         */
        if (is_string($attribute)) {
            $attribute = trim($attribute);

            if ($attribute === '') {
                throw new RuntimeException(
                    'LaraKit Admin password force-change attribute is not configured.'
                );
            }

            return $attribute;
        }

        /*
         * Any other type is an invalid configuration value.
         */
        throw new InvalidArgumentException(
            'LaraKit Admin password force-change attribute must be a string or null.'
        );
    }

    protected function ensureAttribute(
        Authenticatable $user,
        string $attribute
    ): void {
        if (!$user instanceof Model) {
            throw new InvalidArgumentException(
                'The configured user model must extend '
                . 'Illuminate\Database\Eloquent\Model.'
            );
        }

        if (
            !array_key_exists(
                $attribute,
                $user->getAttributes()
            )
            &&
            !array_key_exists(
                $attribute,
                $user->getCasts()
            )
        ) {
            throw new InvalidArgumentException(
                "User model does not support configured password "
                . "force-change attribute [{$attribute}]."
            );
        }
    }
}