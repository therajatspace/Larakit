<?php

namespace Tests\Unit\Admin;

use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\AdminManager;
use Therajatspace\Larakit\Admin\Contracts\AdminManagerContract;

class AdminManagerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            \Therajatspace\Larakit\Admin\AdminServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config()->set(
            'larakit.admin.enabled',
            true
        );

        config()->set(
            'larakit.admin.route_prefix',
            'admin'
        );

        config()->set(
            'larakit.admin.middleware',
            ['web']
        );

        config()->set(
            'larakit.admin.modules',
            [
                'dashboard' => true,
                'users' => false,
                'seo_health' => true,
            ]
        );
    }

    public function test_manager_is_bound_to_contract(): void
    {
        $manager = $this->app->make(
            AdminManagerContract::class
        );

        $this->assertInstanceOf(
            AdminManager::class,
            $manager
        );
    }

    public function test_manager_is_singleton(): void
    {
        $first = $this->app->make(
            AdminManager::class
        );

        $second = $this->app->make(
            AdminManager::class
        );

        $this->assertSame(
            $first,
            $second
        );
    }

    public function test_enabled_returns_true_when_enabled(): void
    {
        $manager = $this->app->make(AdminManager::class);

        $this->assertTrue(
            $manager->enabled()
        );
    }

    public function test_enabled_returns_false_when_disabled(): void
    {
        config([
            'larakit.admin.enabled' => false,
        ]);

        $manager = $this->app->make(AdminManager::class);

        $this->assertFalse(
            $manager->enabled()
        );
    }

    public function test_route_prefix_is_returned(): void
    {
        $manager = $this->app->make(AdminManager::class);

        $this->assertSame(
            'admin',
            $manager->routePrefix()
        );
    }

    public function test_route_prefix_slashes_are_normalized(): void
    {
        config([
            'larakit.admin.route_prefix' => '/backend/admin/',
        ]);

        $manager = $this->app->make(AdminManager::class);

        $this->assertSame(
            'backend/admin',
            $manager->routePrefix()
        );
    }

    public function test_empty_route_prefix_throws_exception(): void
    {
        config([
            'larakit.admin.route_prefix' => '   ',
        ]);

        $manager = $this->app->make(AdminManager::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->routePrefix();
    }

    public function test_non_string_route_prefix_throws_exception(): void
    {
        config([
            'larakit.admin.route_prefix' => ['admin'],
        ]);

        $manager = $this->app->make(AdminManager::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->routePrefix();
    }

    public function test_middleware_is_returned(): void
    {
        $manager = $this->app->make(AdminManager::class);

        $this->assertSame(
            ['web'],
            $manager->middleware()
        );
    }

    public function test_invalid_middleware_configuration_throws_exception(): void
    {
        config([
            'larakit.admin.middleware' => 'web',
        ]);

        $manager = $this->app->make(AdminManager::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->middleware();
    }

    public function test_empty_middleware_values_are_removed(): void
    {
        config([
            'larakit.admin.middleware' => [
                'web',
                '',
                '   ',
                'auth',
            ],
        ]);

        $manager = $this->app->make(AdminManager::class);

        $this->assertSame(
            ['web', 'auth'],
            $manager->middleware()
        );
    }

    public function test_enabled_module_returns_true(): void
    {
        $manager = $this->app->make(AdminManager::class);

        $this->assertTrue(
            $manager->moduleEnabled('dashboard')
        );
    }

    public function test_disabled_module_returns_false(): void
    {
        $manager = $this->app->make(AdminManager::class);

        $this->assertFalse(
            $manager->moduleEnabled('users')
        );
    }

    public function test_unknown_module_returns_false(): void
    {
        $manager = $this->app->make(AdminManager::class);

        $this->assertFalse(
            $manager->moduleEnabled('does_not_exist')
        );
    }

    public function test_empty_module_name_throws_exception(): void
    {
        $manager = $this->app->make(AdminManager::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->moduleEnabled('   ');
    }
}