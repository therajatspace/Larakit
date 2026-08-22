<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Authorization;

use Illuminate\Contracts\Auth\Authenticatable;
use InvalidArgumentException;
use Mockery;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Authorization\UserAuthorizationManager;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;
use Therajatspace\Larakit\LaraKitServiceProvider;

class UserAuthorizationManagerTest extends TestCase
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
            UserAuthorizationManager::class
        );

        $this->assertInstanceOf(
            UserAuthorizationManager::class,
            $manager
        );
    }

    public function test_contract_can_be_resolved(): void
    {
        $manager = $this->app->make(
            UserAuthorizationManagerContract::class
        );

        $this->assertInstanceOf(
            UserAuthorizationManager::class,
            $manager
        );
    }

    public function test_empty_role_is_rejected(): void
    {
        $manager = $this->app->make(
            UserAuthorizationManager::class
        );

        $user = Mockery::mock(Authenticatable::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->assignRole(
            $user,
            '   '
        );
    }

    public function test_empty_permission_is_rejected(): void
    {
        $manager = $this->app->make(
            UserAuthorizationManager::class
        );

        $user = Mockery::mock(Authenticatable::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->givePermission(
            $user,
            '   '
        );
    }

    public function test_empty_role_is_rejected_when_removed(): void
    {
        $manager = $this->app->make(
            UserAuthorizationManager::class
        );

        $user = Mockery::mock(Authenticatable::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->removeRole(
            $user,
            '   '
        );
    }

    public function test_empty_permission_is_rejected_when_revoked(): void
    {
        $manager = $this->app->make(
            UserAuthorizationManager::class
        );

        $user = Mockery::mock(Authenticatable::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->revokePermission(
            $user,
            '   '
        );
    }
}