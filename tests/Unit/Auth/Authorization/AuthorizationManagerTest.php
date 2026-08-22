<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Authorization;

use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Authorization\AuthorizationManager;
use Therajatspace\Larakit\Auth\Contracts\AuthorizationManagerContract;
use Therajatspace\Larakit\LaraKitServiceProvider;

class AuthorizationManagerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
            AuthServiceProvider::class,
        ];
    }

    public function test_manager_can_be_resolved(): void
    {
        $manager = $this->app->make(
            AuthorizationManager::class
        );

        $this->assertInstanceOf(
            AuthorizationManager::class,
            $manager
        );
    }

    public function test_contract_can_be_resolved(): void
    {
        $manager = $this->app->make(
            AuthorizationManagerContract::class
        );

        $this->assertInstanceOf(
            AuthorizationManager::class,
            $manager
        );
    }

    public function test_guard_uses_laravel_default_when_not_configured(): void
    {
        config()->set(
            'auth.defaults.guard',
            'web'
        );

        config()->set(
            'larakit.auth.guard',
            null
        );

        $manager = $this->app->make(
            AuthorizationManager::class
        );

        $this->assertSame(
            'web',
            $manager->guard()
        );
    }

    public function test_custom_guard_is_respected(): void
    {
        config()->set(
            'larakit.auth.guard',
            'admin'
        );

        $manager = $this->app->make(
            AuthorizationManager::class
        );

        $this->assertSame(
            'admin',
            $manager->guard()
        );
    }

    public function test_authorization_is_enabled_when_auth_is_enabled(): void
    {
        config()->set(
            'larakit.auth.enabled',
            true
        );

        $manager = $this->app->make(
            AuthorizationManager::class
        );

        $this->assertTrue(
            $manager->enabled()
        );
    }

    public function test_authorization_is_disabled_when_auth_is_disabled(): void
    {
        config()->set(
            'larakit.auth.enabled',
            false
        );

        $manager = $this->app->make(
            AuthorizationManager::class
        );

        $this->assertFalse(
            $manager->enabled()
        );
    }

    public function test_ensure_supported_rejects_disabled_authentication(): void
    {
        config()->set(
            'larakit.auth.enabled',
            false
        );

        $manager = $this->app->make(
            AuthorizationManager::class
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->ensureSupported();
    }
}