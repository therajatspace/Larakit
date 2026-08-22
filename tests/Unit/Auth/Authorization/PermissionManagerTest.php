<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Authorization;

use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Authorization\PermissionManager;
use Therajatspace\Larakit\Auth\Contracts\PermissionManagerContract;
use Therajatspace\Larakit\LaraKitServiceProvider;

class PermissionManagerTest extends TestCase
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
            PermissionManager::class
        );

        $this->assertInstanceOf(
            PermissionManager::class,
            $manager
        );
    }

    public function test_contract_can_be_resolved(): void
    {
        $manager = $this->app->make(
            PermissionManagerContract::class
        );

        $this->assertInstanceOf(
            PermissionManager::class,
            $manager
        );
    }

    public function test_empty_permission_name_is_rejected(): void
    {
        $manager = $this->app->make(
            PermissionManager::class
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->create('   ');
    }

    public function test_permission_name_longer_than_255_characters_is_rejected(): void
    {
        $manager = $this->app->make(
            PermissionManager::class
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->create(
            str_repeat('a', 256)
        );
    }
}