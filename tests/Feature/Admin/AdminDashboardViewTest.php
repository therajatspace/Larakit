<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\AdminServiceProvider;
use Therajatspace\Larakit\LaraKitServiceProvider;

class AdminDashboardViewTest extends TestCase
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

        $app['config']->set(
            'larakit.admin.modules.users',
            true
        );

        $app['config']->set(
            'larakit.admin.modules.authorization',
            true
        );

        $app['config']->set(
            'larakit.admin.modules.website_health',
            true
        );

        $app['config']->set(
            'larakit.admin.modules.seo_health',
            true
        );

        $app['config']->set(
            'larakit.admin.modules.traffic',
            true
        );

        $app['config']->set(
            'larakit.admin.modules.audit',
            true
        );
    }

    protected function authenticate(): void
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
    }

    public function test_dashboard_route_renders_dashboard_view(): void
    {
        $this->authenticate();

        $response = $this->get(
            route('larakit.admin.dashboard')
        );

        $response->assertOk();

        $response->assertSee(
            'Dashboard'
        );

        $response->assertSee(
            'Welcome to the LaraKit administration panel.'
        );
    }

    public function test_dashboard_renders_admin_navigation(): void
    {
        $this->authenticate();

        $response = $this->get(
            route('larakit.admin.dashboard')
        );

        $response->assertOk();

        $response->assertSee(
            'LaraKit'
        );

        $response->assertSee(
            'Administration'
        );

        $response->assertSee(
            'Dashboard'
        );

        $response->assertSee(
            'Users'
        );

        $response->assertSee(
            'Authorization'
        );

        $response->assertSee(
            'Website Health'
        );

        $response->assertSee(
            'SEO Health'
        );

        $response->assertSee(
            'Traffic'
        );

        $response->assertSee(
            'Audit'
        );
    }

    public function test_dashboard_route_still_exists(): void
    {
        $this->assertTrue(
            \Illuminate\Support\Facades\Route::has(
                'larakit.admin.dashboard'
            )
        );
    }
}