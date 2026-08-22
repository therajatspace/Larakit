<?php

namespace Therajatspace\Larakit\Auth\Users;

use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;
use Therajatspace\Larakit\Auth\Contracts\UserResolverContract;

class UserResolver implements UserResolverContract
{
    public function guard(): string
    {
        $guard = config(
            'larakit.auth.guard'
        );

        if ($guard === null) {
            $guard = config(
                'auth.defaults.guard'
            );
        }

        if (!is_string($guard) || trim($guard) === '') {
            throw new InvalidArgumentException(
                'LaraKit could not resolve an authentication guard.'
            );
        }

        $configuration = config(
            "auth.guards.{$guard}"
        );

        if (!is_array($configuration)) {
            throw new InvalidArgumentException(
                "LaraKit authentication guard [{$guard}] is not configured."
            );
        }

        return $guard;
    }

    public function provider(): string
    {
        $guard = $this->guard();

        $provider = config(
            "auth.guards.{$guard}.provider"
        );

        if (!is_string($provider) || trim($provider) === '') {
            throw new InvalidArgumentException(
                "LaraKit authentication guard [{$guard}] "
                . 'does not define an authentication provider.'
            );
        }

        $configuration = config(
            "auth.providers.{$provider}"
        );

        if (!is_array($configuration)) {
            throw new InvalidArgumentException(
                "LaraKit authentication provider [{$provider}] "
                . 'is not configured.'
            );
        }

        return $provider;
    }

    public function model(): string
    {
        $configuredModel = config(
            'larakit.auth.user.model'
        );

        $model = $configuredModel;

        if ($model === null) {
            $provider = $this->provider();

            $model = config(
                "auth.providers.{$provider}.model"
            );
        }

        if (!is_string($model) || trim($model) === '') {
            throw new InvalidArgumentException(
                'LaraKit could not resolve an authentication user model.'
            );
        }

        if (!class_exists($model)) {
            throw new InvalidArgumentException(
                "LaraKit authentication user model [{$model}] does not exist."
            );
        }

        if (!is_subclass_of($model, Authenticatable::class)) {
            throw new InvalidArgumentException(
                "LaraKit authentication user model [{$model}] must "
                . 'implement Illuminate\Contracts\Auth\Authenticatable.'
            );
        }

        return $model;
    }
}