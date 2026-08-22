<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Authorization;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Mockery;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Authorization\DelegationManager;
use Therajatspace\Larakit\Auth\Authorization\DelegationService;
use Therajatspace\Larakit\Auth\Contracts\DelegationServiceContract;
use Therajatspace\Larakit\Auth\Contracts\UserAuthorizationManagerContract;
use Therajatspace\Larakit\LaraKitServiceProvider;

class DelegationServiceTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
            AuthServiceProvider::class,
        ];
    }

    public function test_service_can_be_resolved(): void
    {
        $service = $this->app->make(
            DelegationService::class
        );

        $this->assertInstanceOf(
            DelegationService::class,
            $service
        );
    }

    public function test_contract_can_be_resolved(): void
    {
        $service = $this->app->make(
            DelegationServiceContract::class
        );

        $this->assertInstanceOf(
            DelegationService::class,
            $service
        );
    }

    public function test_self_role_delegation_is_rejected(): void
    {
        $actor = Mockery::mock(
            Authenticatable::class
        );

        $target = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('getAuthIdentifier')
            ->once()
            ->andReturn(10);

        $target->shouldReceive('getAuthIdentifier')
            ->once()
            ->andReturn(10);

        $delegation = Mockery::mock(
            DelegationManager::class
        );

        $authorization = Mockery::mock(
            UserAuthorizationManagerContract::class
        );

        $service = new DelegationService(
            $delegation,
            $authorization
        );

        $this->expectException(
            AuthorizationException::class
        );

        $service->assignRole(
            $actor,
            $target,
            'manager'
        );
    }

    public function test_self_permission_delegation_is_rejected(): void
    {
        $actor = Mockery::mock(
            Authenticatable::class
        );

        $target = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('getAuthIdentifier')
            ->once()
            ->andReturn(10);

        $target->shouldReceive('getAuthIdentifier')
            ->once()
            ->andReturn(10);

        $delegation = Mockery::mock(
            DelegationManager::class
        );

        $authorization = Mockery::mock(
            UserAuthorizationManagerContract::class
        );

        $service = new DelegationService(
            $delegation,
            $authorization
        );

        $this->expectException(
            AuthorizationException::class
        );

        $service->assignPermission(
            $actor,
            $target,
            'articles.edit'
        );
    }

    public function test_empty_role_is_rejected(): void
    {
        $actor = Mockery::mock(
            Authenticatable::class
        );

        $target = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('getAuthIdentifier')
            ->once()
            ->andReturn(10);

        $target->shouldReceive('getAuthIdentifier')
            ->once()
            ->andReturn(20);

        $delegation = Mockery::mock(
            DelegationManager::class
        );

        $authorization = Mockery::mock(
            UserAuthorizationManagerContract::class
        );

        $service = new DelegationService(
            $delegation,
            $authorization
        );

        $this->expectException(
            AuthorizationException::class
        );

        $service->assignRole(
            $actor,
            $target,
            '   '
        );
    }

    public function test_empty_permission_is_rejected(): void
    {
        $actor = Mockery::mock(
            Authenticatable::class
        );

        $target = Mockery::mock(
            Authenticatable::class
        );

        $actor->shouldReceive('getAuthIdentifier')
            ->once()
            ->andReturn(10);

        $target->shouldReceive('getAuthIdentifier')
            ->once()
            ->andReturn(20);

        $delegation = Mockery::mock(
            DelegationManager::class
        );

        $authorization = Mockery::mock(
            UserAuthorizationManagerContract::class
        );

        $service = new DelegationService(
            $delegation,
            $authorization
        );

        $this->expectException(
            AuthorizationException::class
        );

        $service->assignPermission(
            $actor,
            $target,
            '   '
        );
    }
}