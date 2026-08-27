<?php

namespace Tests\Feature\Admin;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\AdminRouteRegistrar;
use Therajatspace\Larakit\Admin\AdminServiceProvider;
use Therajatspace\Larakit\LaraKitServiceProvider;

class AdminUserRouteTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
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
            []
        );

        $app['config']->set(
            'larakit.admin.access.permission',
            null
        );

        $app['config']->set(
            'larakit.admin.modules.dashboard',
            true
        );

        $app['config']->set(
            'larakit.admin.modules.users',
            true
        );
    }

    protected function registerRoutes(): void
    {
        $this->app->make(
            AdminRouteRegistrar::class
        )->register();
    }

    public function test_users_index_route_exists(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.users.index'
        );

        $this->assertNotNull($route);
    }

    public function test_users_show_route_exists(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.users.show'
        );

        $this->assertNotNull($route);
    }

    public function test_users_index_route_uses_expected_uri(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.users.index'
        );

        $this->assertNotNull($route);

        $this->assertSame(
            'admin/users',
            $route->uri()
        );
    }

    public function test_users_show_route_uses_expected_uri(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.users.show'
        );

        $this->assertNotNull($route);

        $this->assertSame(
            'admin/users/{id}',
            $route->uri()
        );
    }

    public function test_users_index_route_uses_get_method(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.users.index'
        );

        $this->assertNotNull($route);

        $this->assertContains(
            'GET',
            $route->methods()
        );
    }

    public function test_users_show_route_uses_get_method(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.users.show'
        );

        $this->assertNotNull($route);

        $this->assertContains(
            'GET',
            $route->methods()
        );
    }

    public function test_users_index_route_points_to_user_controller(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.users.index'
        );

        $this->assertNotNull($route);

        $this->assertSame(
            \Therajatspace\Larakit\Admin\Users\Http\Controllers\UserController::class
            . '@index',
            $route->getAction('uses')
        );
    }

    public function test_users_show_route_points_to_user_controller(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.users.show'
        );

        $this->assertNotNull($route);

        $this->assertSame(
            \Therajatspace\Larakit\Admin\Users\Http\Controllers\UserController::class
            . '@show',
            $route->getAction('uses')
        );
    }

    public function test_users_routes_use_admin_access_middleware(): void
    {
        $this->registerRoutes();

        $indexRoute = Route::getRoutes()->getByName(
            'larakit.admin.users.index'
        );

        $showRoute = Route::getRoutes()->getByName(
            'larakit.admin.users.show'
        );

        $this->assertNotNull($indexRoute);
        $this->assertNotNull($showRoute);

        $this->assertContains(
            \Therajatspace\Larakit\Admin\Access\AdminAccessMiddleware::class,
            $indexRoute->gatherMiddleware()
        );

        $this->assertContains(
            \Therajatspace\Larakit\Admin\Access\AdminAccessMiddleware::class,
            $showRoute->gatherMiddleware()
        );
    }

    public function test_users_index_route_has_expected_name(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.users.index'
        );

        $this->assertNotNull($route);

        $this->assertSame(
            'larakit.admin.users.index',
            $route->getName()
        );
    }

    public function test_users_show_route_has_expected_name(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.users.show'
        );

        $this->assertNotNull($route);

        $this->assertSame(
            'larakit.admin.users.show',
            $route->getName()
        );
    }

    public function test_dashboard_route_exists(): void
    {
        $this->registerRoutes();

        $route = Route::getRoutes()->getByName(
            'larakit.admin.dashboard'
        );

        $this->assertNotNull($route);
    }
}