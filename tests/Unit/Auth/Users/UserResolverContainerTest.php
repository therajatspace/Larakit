<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Users;

use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\Contracts\UserResolverContract;
use Therajatspace\Larakit\Auth\Users\UserResolver;
use Therajatspace\Larakit\LaraKitServiceProvider;

class UserResolverContainerTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
        ];
    }

    public function test_user_resolver_can_be_resolved(): void
    {
        $resolver = $this->app->make(
            UserResolver::class
        );

        $this->assertInstanceOf(
            UserResolver::class,
            $resolver
        );
    }

    public function test_user_resolver_contract_can_be_resolved(): void
    {
        $resolver = $this->app->make(
            UserResolverContract::class
        );

        $this->assertInstanceOf(
            UserResolver::class,
            $resolver
        );
    }
}