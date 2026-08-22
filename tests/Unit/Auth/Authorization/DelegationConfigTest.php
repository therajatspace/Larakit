<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Authorization;

use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Authorization\DelegationConfig;
use Therajatspace\Larakit\Auth\Contracts\DelegationConfigContract;
use Therajatspace\Larakit\LaraKitServiceProvider;

class DelegationConfigTest extends TestCase
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
            DelegationConfig::class
        );

        $this->assertInstanceOf(
            DelegationConfig::class,
            $manager
        );
    }

    public function test_contract_can_be_resolved(): void
    {
        $manager = $this->app->make(
            DelegationConfigContract::class
        );

        $this->assertInstanceOf(
            DelegationConfig::class,
            $manager
        );
    }

    public function test_delegation_is_enabled_by_default(): void
    {
        config()->set(
            'larakit.auth.delegation.enabled',
            true
        );

        $manager = app(
            DelegationConfig::class
        );

        $this->assertTrue(
            $manager->enabled()
        );
    }

    public function test_delegation_can_be_disabled(): void
    {
        config()->set(
            'larakit.auth.delegation.enabled',
            false
        );

        $manager = app(
            DelegationConfig::class
        );

        $this->assertFalse(
            $manager->enabled()
        );
    }

    public function test_role_targets_are_resolved(): void
    {
        config()->set(
            'larakit.auth.delegation.roles.manager.assignable',
            [
                'employee',
                'assistant',
            ]
        );

        $manager = app(
            DelegationConfig::class
        );

        $this->assertSame(
            [
                'employee',
                'assistant',
            ],
            $manager->roleTargets('manager')
        );
    }

    public function test_permission_targets_are_resolved(): void
    {
        config()->set(
            'larakit.auth.delegation.permissions.manager.assignable',
            [
                'articles.view',
                'articles.edit',
            ]
        );

        $manager = app(
            DelegationConfig::class
        );

        $this->assertSame(
            [
                'articles.view',
                'articles.edit',
            ],
            $manager->permissionTargets('manager')
        );
    }

    public function test_targets_are_trimmed_and_deduplicated(): void
    {
        config()->set(
            'larakit.auth.delegation.roles.manager.assignable',
            [
                ' employee ',
                'employee',
                '',
                '   ',
                'assistant',
            ]
        );

        $manager = app(
            DelegationConfig::class
        );

        $this->assertSame(
            [
                'employee',
                'assistant',
            ],
            $manager->roleTargets('manager')
        );
    }

    public function test_missing_role_configuration_returns_empty_array(): void
    {
        config()->set(
            'larakit.auth.delegation.roles',
            []
        );

        $manager = app(
            DelegationConfig::class
        );

        $this->assertSame(
            [],
            $manager->roleTargets('manager')
        );
    }
}