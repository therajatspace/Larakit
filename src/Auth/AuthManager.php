<?php

namespace Therajatspace\Larakit\Auth;

use InvalidArgumentException;
use Therajatspace\Larakit\Auth\Contracts\AuthManagerContract;

class AuthManager implements AuthManagerContract
{
    protected const ROUTE_MODES = [
        'dedicated',
        'shared',
    ];

    public function enabled(): bool
    {
        return (bool) config('larakit.auth.enabled', false);
    }

    public function routeMode(): string
    {
        $mode = config(
            'larakit.auth.route_mode',
            'dedicated'
        );

        if (!in_array($mode, self::ROUTE_MODES, true)) {
            throw new InvalidArgumentException(
                "Invalid LaraKit authentication route mode [{$mode}]. "
                . 'Supported modes are: dedicated, shared.'
            );
        }

        return $mode;
    }
}