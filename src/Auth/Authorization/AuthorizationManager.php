<?php

namespace Therajatspace\Larakit\Auth\Authorization;

use InvalidArgumentException;
use Therajatspace\Larakit\Auth\Context\AuthContext;

class AuthorizationManager
{
    public function __construct(
        protected AuthContext $context,
    ) {
    }

    public function enabled(): bool
    {
        return (bool) config(
            'larakit.auth.enabled',
            false
        );
    }

    public function guard(): string
    {
        $guard = config(
            'larakit.auth.guard'
        );

        if ($guard === null || $guard === '') {
            return config(
                'auth.defaults.guard',
                'web'
            );
        }

        return $guard;
    }

    public function userModel(): string
    {
        return $this->context->userModel();
    }

    public function supportsRoles(): bool
    {
        $modelClass = $this->userModel();

        return in_array(
            'Spatie\\Permission\\Traits\\HasRoles',
            class_uses_recursive($modelClass),
            true
        );
    }

    public function ensureSupported(): void
    {
        if (!$this->enabled()) {
            throw new InvalidArgumentException(
                'LaraKit authentication is disabled.'
            );
        }

        if (!$this->supportsRoles()) {
            throw new InvalidArgumentException(
                "LaraKit authentication user model [{$this->userModel()}] must use Spatie's HasRoles trait."
            );
        }
    }
}