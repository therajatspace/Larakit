<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Authorization;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Mockery;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Authorization\DelegationManager;
use Therajatspace\Larakit\Auth\Authorization\DelegationConfig;
use Therajatspace\Larakit\Auth\Contracts\DelegationManagerContract;
use Therajatspace\Larakit\LaraKitServiceProvider;

class DelegationManagerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
            AuthServiceProvider::class,
        ];
    }

    protected function manager(): DelegationManager
    {
        return $this->app->make(
            DelegationManager::class
        );
    }

    public function test_manager_can_be_resolved(): void
    {
        $this->assertInstanceOf(
            DelegationManager::class,
            $this->manager()
        );
    }

    public function test_contract_can_be_resolved(): void
    {
        $manager = $this->app->make(
            DelegationManagerContract::class
        );

        $this->assertInstanceOf(
            DelegationManager::class,
            $manager
        );
    }

    public function test_role_assignment_requires_permission(): void
    {
        $actor = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('can')
            ->once()
            ->with('roles.assign')
            ->andReturn(false);

        $manager = $this->manager();

        $this->expectException(
            AuthorizationException::class
        );

        $manager->ensureCanAssignRole(
            $actor,
            'editor'
        );
    }

    public function test_permission_assignment_requires_permission(): void
    {
        $actor = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('can')
            ->once()
            ->with('permissions.assign')
            ->andReturn(false);

        $manager = $this->manager();

        $this->expectException(
            AuthorizationException::class
        );

        $manager->ensureCanAssignPermission(
            $actor,
            'articles.edit'
        );
    }

    public function test_existing_role_can_be_delegated(): void
    {
        $actor = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('can')
            ->once()
            ->with('roles.assign')
            ->andReturn(true);

        $actor->shouldReceive('hasRole')
            ->once()
            ->with('editor')
            ->andReturn(true);

        $manager = $this->manager();

        $manager->ensureCanAssignRole(
            $actor,
            'editor'
        );

        $this->assertTrue(true);
    }

    public function test_configured_role_can_be_delegated(): void
    {
        config()->set(
            'larakit.auth.delegation.roles.manager.assignable',
            [
                'employee',
            ]
        );

        $actor = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('can')
            ->once()
            ->with('roles.assign')
            ->andReturn(true);

        $actor->shouldReceive('hasRole')
            ->once()
            ->with('employee')
            ->andReturn(false);

        $actor->shouldReceive('getRoleNames')
            ->once()
            ->andReturn([
                'manager',
            ]);

        $manager = $this->manager();

        $manager->ensureCanAssignRole(
            $actor,
            'employee'
        );

        $this->assertTrue(true);
    }

    public function test_unconfigured_role_cannot_be_delegated(): void
    {
        config()->set(
            'larakit.auth.delegation.roles.manager.assignable',
            [
                'employee',
            ]
        );

        $actor = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('can')
            ->once()
            ->with('roles.assign')
            ->andReturn(true);

        $actor->shouldReceive('hasRole')
            ->once()
            ->with('admin')
            ->andReturn(false);

        $actor->shouldReceive('getRoleNames')
            ->once()
            ->andReturn([
                'manager',
            ]);

        $manager = $this->manager();

        $this->expectException(
            AuthorizationException::class
        );

        $manager->ensureCanAssignRole(
            $actor,
            'admin'
        );
    }

    public function test_existing_permission_can_be_delegated(): void
    {
        $actor = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('can')
            ->once()
            ->with('permissions.assign')
            ->andReturn(true);

        $actor->shouldReceive('can')
            ->once()
            ->with('articles.edit')
            ->andReturn(true);

        $manager = $this->manager();

        $manager->ensureCanAssignPermission(
            $actor,
            'articles.edit'
        );

        $this->assertTrue(true);
    }

    public function test_configured_permission_can_be_delegated(): void
    {
        config()->set(
            'larakit.auth.delegation.permissions.manager.assignable',
            [
                'articles.view',
            ]
        );

        $actor = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('can')
            ->once()
            ->with('permissions.assign')
            ->andReturn(true);

        $actor->shouldReceive('can')
            ->once()
            ->with('articles.view')
            ->andReturn(false);

        $actor->shouldReceive('getRoleNames')
            ->once()
            ->andReturn([
                'manager',
            ]);

        $manager = $this->manager();

        $manager->ensureCanAssignPermission(
            $actor,
            'articles.view'
        );

        $this->assertTrue(true);
    }

    public function test_unconfigured_permission_cannot_be_delegated(): void
    {
        config()->set(
            'larakit.auth.delegation.permissions.manager.assignable',
            [
                'articles.view',
            ]
        );

        $actor = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('can')
            ->once()
            ->with('permissions.assign')
            ->andReturn(true);

        $actor->shouldReceive('can')
            ->once()
            ->with('articles.delete')
            ->andReturn(false);

        $actor->shouldReceive('getRoleNames')
            ->once()
            ->andReturn([
                'manager',
            ]);

        $manager = $this->manager();

        $this->expectException(
            AuthorizationException::class
        );

        $manager->ensureCanAssignPermission(
            $actor,
            'articles.delete'
        );
    }

    public function test_disabled_delegation_rejects_role_assignment(): void
    {
        config()->set(
            'larakit.auth.delegation.enabled',
            false
        );

        $actor = Mockery::mock(
            Authenticatable::class
        );

        $manager = $this->manager();

        $this->expectException(
            AuthorizationException::class
        );

        $manager->ensureCanAssignRole(
            $actor,
            'editor'
        );
    }
}