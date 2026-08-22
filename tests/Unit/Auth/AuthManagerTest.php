<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth;

use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthManager;
use Therajatspace\Larakit\Auth\Contracts\AuthManagerContract;
use Therajatspace\Larakit\LaraKitServiceProvider;

class AuthManagerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
        ];
    }

    public function test_auth_manager_can_be_resolved(): void
    {
        $manager = $this->app->make(AuthManager::class);

        $this->assertInstanceOf(
            AuthManager::class,
            $manager
        );
    }

    public function test_auth_manager_contract_can_be_resolved(): void
    {
        $manager = $this->app->make(AuthManagerContract::class);

        $this->assertInstanceOf(
            AuthManager::class,
            $manager
        );
    }

    public function test_auth_manager_reports_enabled_state(): void
    {
        config()->set('larakit.auth.enabled', true);

        $manager = $this->app->make(AuthManager::class);

        $this->assertTrue($manager->enabled());
    }

    public function test_auth_manager_reports_disabled_state(): void
    {
        config()->set('larakit.auth.enabled', false);

        $manager = $this->app->make(AuthManager::class);

        $this->assertFalse($manager->enabled());
    }

    public function test_auth_route_mode_defaults_to_dedicated(): void
    {
        config()->set(
            'larakit.auth.route_mode',
            'dedicated'
        );

        $manager = $this->app->make(AuthManager::class);

        $this->assertSame(
            'dedicated',
            $manager->routeMode()
        );
    }

    public function test_auth_route_mode_supports_shared(): void
    {
        config()->set(
            'larakit.auth.route_mode',
            'shared'
        );

        $manager = $this->app->make(AuthManager::class);

        $this->assertSame(
            'shared',
            $manager->routeMode()
        );
    }

    public function test_invalid_auth_route_mode_is_rejected(): void
    {
        config()->set(
            'larakit.auth.route_mode',
            'invalid'
        );

        $manager = $this->app->make(AuthManager::class);

        $this->expectException(\InvalidArgumentException::class);

        $manager->routeMode();
    }
}