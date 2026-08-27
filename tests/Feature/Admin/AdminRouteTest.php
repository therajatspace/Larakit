<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\AdminServiceProvider;

class AdminRouteTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            AdminServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set(
            'larakit.admin.enabled',
            true
        );

        $app['config']->set(
            'larakit.admin.route_prefix',
            'admin'
        );

        $app['config']->set(
            'larakit.admin.middleware',
            ['web']
        );

        $app['config']->set(
            'larakit.admin.modules.dashboard',
            true
        );
    }

    public function test_admin_dashboard_route_exists(): void
    {
        $this->assertTrue(
            Route::has('larakit.admin.dashboard')
        );
    }

    public function test_admin_dashboard_route_uses_expected_uri(): void
    {
        $route = Route::getRoutes()->getByName(
            'larakit.admin.dashboard'
        );

        $this->assertNotNull($route);

        $this->assertSame(
            'admin',
            $route->uri()
        );
    }

    public function test_admin_dashboard_route_uses_get_method(): void
    {
        $route = Route::getRoutes()->getByName(
            'larakit.admin.dashboard'
        );

        $this->assertNotNull($route);

        $this->assertContains(
            'GET',
            $route->methods()
        );
    }

    public function test_admin_dashboard_route_contains_configured_middleware(): void
    {
        $route = Route::getRoutes()->getByName(
            'larakit.admin.dashboard'
        );

        $this->assertNotNull($route);

        $middleware = $route->gatherMiddleware();

        $this->assertContains(
            'web',
            $middleware
        );
    }

    public function test_admin_dashboard_route_contains_admin_access_middleware(): void
    {
        $route = Route::getRoutes()->getByName(
            'larakit.admin.dashboard'
        );

        $this->assertNotNull($route);

        $middleware = $route->gatherMiddleware();

        $this->assertContains(
            \Therajatspace\Larakit\Admin\Access\AdminAccessMiddleware::class,
            $middleware
        );
    }
}