<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthRouteRegistrar;
use Therajatspace\Larakit\LaraKitServiceProvider;

class AuthRouteRegistrarTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
        ];
    }

    public function test_auth_routes_are_registered_in_dedicated_mode(): void
    {
        $this->assertTrue(
            Route::has('larakit.login')
        );

        $this->assertTrue(
            Route::has('larakit.logout')
        );
    }

    public function test_login_route_uses_post_method(): void
    {
        $route = Route::getRoutes()->getByName(
            'larakit.login'
        );

        $this->assertNotNull($route);

        $this->assertSame(
            ['POST'],
            $route->methods()
        );
    }

    public function test_logout_route_uses_post_method(): void
    {
        $route = Route::getRoutes()->getByName(
            'larakit.logout'
        );

        $this->assertNotNull($route);

        $this->assertSame(
            ['POST'],
            $route->methods()
        );
    }

    public function test_registrar_can_be_resolved(): void
    {
        $this->assertInstanceOf(
            AuthRouteRegistrar::class,
            $this->app->make(
                AuthRouteRegistrar::class
            )
        );
    }
}
