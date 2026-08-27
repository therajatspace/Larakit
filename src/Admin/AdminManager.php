<?php

namespace Therajatspace\Larakit\Admin;

use InvalidArgumentException;
use Therajatspace\Larakit\Admin\Contracts\AdminManagerContract;

class AdminManager implements AdminManagerContract
{
    public function enabled(): bool
    {
        return (bool) config(
            'larakit.admin.enabled',
            false
        );
    }

    public function routePrefix(): string
    {
        $prefix = config(
            'larakit.admin.route_prefix',
            'admin'
        );

        if (!is_string($prefix)) {
            throw new InvalidArgumentException(
                'LaraKit admin route prefix must be a string.'
            );
        }

        $prefix = trim($prefix);

        if ($prefix === '') {
            throw new InvalidArgumentException(
                'LaraKit admin route prefix cannot be empty.'
            );
        }

        return trim($prefix, '/');
    }

    /**
     * @return array<int, string>
     */
    public function middleware(): array
    {
        $middleware = config(
            'larakit.admin.middleware',
            ['web']
        );

        if (!is_array($middleware)) {
            throw new InvalidArgumentException(
                'LaraKit admin middleware configuration must be an array.'
            );
        }

        return array_values(
            array_filter(
                $middleware,
                static function ($value): bool {
                    return is_string($value)
                        && trim($value) !== '';
                }
            )
        );
    }

    public function moduleEnabled(string $module): bool
    {
        if (trim($module) === '') {
            throw new InvalidArgumentException(
                'LaraKit admin module name cannot be empty.'
            );
        }

        return (bool) config(
            "larakit.admin.modules.{$module}",
            false
        );
    }
}