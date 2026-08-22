<?php

namespace Therajatspace\Larakit\Auth;

use Illuminate\Support\Facades\Route;

class AuthRouteRegistrar
{
    public function register(): void
    {
        if (! $this->enabled()) {
            return;
        }

        if ($this->routeMode() !== 'dedicated') {
            return;
        }

        Route::middleware('web')
            ->group(__DIR__.'/routes.php');
    }

    protected function enabled(): bool
    {
        return (bool) config(
            'larakit.auth.enabled',
            true
        );
    }

    protected function routeMode(): string
    {
        return (string) config(
            'larakit.auth.route_mode',
            'dedicated'
        );
    }
}