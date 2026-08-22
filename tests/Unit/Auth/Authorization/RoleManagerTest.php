<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Authorization;

use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Authorization\RoleManager;
use Therajatspace\Larakit\Auth\Contracts\RoleManagerContract;
use Therajatspace\Larakit\LaraKitServiceProvider;

class RoleManagerTest extends TestCase
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
            RoleManager::class
        );

        $this->assertInstanceOf(
            RoleManager::class,
            $manager
        );
    }

    public function test_contract_can_be_resolved(): void
    {
        $manager = $this->app->make(
            RoleManagerContract::class
        );

        $this->assertInstanceOf(
            RoleManager::class,
            $manager
        );
    }

    public function test_empty_role_name_is_rejected(): void
    {
        $manager = $this->app->make(
            RoleManager::class
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->create('   ');
    }

    public function test_role_name_longer_than_255_characters_is_rejected(): void
    {
        $manager = $this->app->make(
            RoleManager::class
        );

        $this->expectException(
            InvalidArgumentException::class
        );

        $manager->create(
            str_repeat('a', 256)
        );
    }
}