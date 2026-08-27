<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\AdminServiceProvider;

class AdminAccessMiddlewareTest extends TestCase
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
            'app.key',
            'base64:' . base64_encode(
                random_bytes(32)
            )
        );

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
            'admin.access'
        );

        $app['config']->set(
            'larakit.admin.modules.dashboard',
            true
        );
    }

    public function test_guest_receives_401(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_without_permission_receives_403(): void
    {
        $user = new class extends Authenticatable {
            public function can(
                $ability,
                $arguments = []
            ) {
                return false;
            }
        };

        $this->be($user);

        $response = $this->get('/admin');

        $response->assertStatus(403);
    }

    public function test_authorized_user_can_access_dashboard(): void
    {
        $user = new class extends Authenticatable {
            public function can(
                $ability,
                $arguments = []
            ) {
                return $ability === 'admin.access';
            }
        };

        $this->be($user);

        $response = $this->get('/admin');

        $response->assertOk();

        $response->assertSee(
            'LaraKit'
        );

        $response->assertSee(
            'Dashboard'
        );
    }

    public function test_admin_access_middleware_is_attached_to_dashboard_route(): void
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