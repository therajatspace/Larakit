<?php

namespace Tests\Unit\Admin\Users\Account;

use Illuminate\Contracts\Auth\Authenticatable;
use Mockery;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Admin\AdminServiceProvider;
use Therajatspace\Larakit\Admin\Users\Account\UserAccountManager;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountDriverContract;
use Therajatspace\Larakit\Admin\Users\Contracts\UserAccountManagerContract;
use Therajatspace\Larakit\LaraKitServiceProvider;

class UserAccountManagerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
            AdminServiceProvider::class,
        ];
    }

    public function test_manager_can_be_resolved(): void
    {
        $manager = $this->app->make(
            UserAccountManager::class
        );

        $this->assertInstanceOf(
            UserAccountManager::class,
            $manager
        );
    }

    public function test_contract_can_be_resolved(): void
    {
        $manager = $this->app->make(
            UserAccountManagerContract::class
        );

        $this->assertInstanceOf(
            UserAccountManager::class,
            $manager
        );
    }

    public function test_activate_delegates_to_driver(): void
    {
        $user = Mockery::mock(
            Authenticatable::class
        );

        $driver = Mockery::mock(
            UserAccountDriverContract::class
        );

        $driver
            ->shouldReceive('activate')
            ->once()
            ->with($user);

        $manager = new UserAccountManager(
            $driver
        );

        $manager->activate(
            $user
        );
    }

    public function test_deactivate_delegates_to_driver(): void
    {
        $user = Mockery::mock(
            Authenticatable::class
        );

        $driver = Mockery::mock(
            UserAccountDriverContract::class
        );

        $driver
            ->shouldReceive('deactivate')
            ->once()
            ->with($user);

        $manager = new UserAccountManager(
            $driver
        );

        $manager->deactivate(
            $user
        );
    }

    public function test_delete_delegates_to_driver(): void
    {
        $user = Mockery::mock(
            Authenticatable::class
        );

        $driver = Mockery::mock(
            UserAccountDriverContract::class
        );

        $driver
            ->shouldReceive('delete')
            ->once()
            ->with($user);

        $manager = new UserAccountManager(
            $driver
        );

        $manager->delete(
            $user
        );
    }

    public function test_manager_passes_exact_user_instance_to_driver(): void
    {
        $user = Mockery::mock(
            Authenticatable::class
        );

        $driver = Mockery::mock(
            UserAccountDriverContract::class
        );

        $driver
            ->shouldReceive('activate')
            ->once()
            ->withArgs(
                static function ($received) use ($user): bool {
                    return $received === $user;
                }
            );

        $manager = new UserAccountManager(
            $driver
        );

        $manager->activate(
            $user
        );
    }

    public function test_manager_does_not_require_eloquent_user_instance(): void
    {
        $user = Mockery::mock(
            Authenticatable::class
        );

        $driver = Mockery::mock(
            UserAccountDriverContract::class
        );

        $driver
            ->shouldReceive('deactivate')
            ->once()
            ->with($user);

        $manager = new UserAccountManager(
            $driver
        );

        $manager->deactivate(
            $user
        );
    }
}
