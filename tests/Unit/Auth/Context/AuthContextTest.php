<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Context;

use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\Context\AuthContext;
use Therajatspace\Larakit\Auth\Contracts\AuthContextContract;
use Therajatspace\Larakit\Auth\Contracts\UserResolverContract;
use Therajatspace\Larakit\LaraKitServiceProvider;
use Therajatspace\Larakit\Tests\Fixtures\TestUser;

class AuthContextTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        config()->set(
            'auth.defaults.guard',
            'web'
        );

        config()->set(
            'auth.guards.web',
            [
                'driver' => 'session',
                'provider' => 'users',
            ]
        );

        config()->set(
            'auth.providers.users',
            [
                'driver' => 'eloquent',
                'model' => TestUser::class,
            ]
        );

        config()->set(
            'larakit.auth.guard',
            null
        );

        config()->set(
            'larakit.auth.user.model',
            null
        );
    }

    public function test_context_resolves_guard(): void
    {
        $context = $this->app->make(AuthContext::class);

        $this->assertSame(
            'web',
            $context->guard()
        );
    }

    public function test_context_resolves_provider(): void
    {
        $context = $this->app->make(AuthContext::class);

        $this->assertSame(
            'users',
            $context->provider()
        );
    }

    public function test_context_resolves_user_model(): void
    {
        $context = $this->app->make(AuthContext::class);

        $this->assertSame(
            TestUser::class,
            $context->userModel()
        );
    }

    public function test_context_contract_can_be_resolved(): void
    {
        $context = $this->app->make(
            AuthContextContract::class
        );

        $this->assertInstanceOf(
            AuthContext::class,
            $context
        );
    }

    public function test_context_uses_the_user_resolver(): void
    {
        $resolver = $this->createMock(
            UserResolverContract::class
        );

        $resolver
            ->expects($this->once())
            ->method('guard')
            ->willReturn('custom');

        $resolver
            ->expects($this->once())
            ->method('provider')
            ->willReturn('custom_provider');

        $resolver
            ->expects($this->once())
            ->method('model')
            ->willReturn(TestUser::class);

        $context = new AuthContext($resolver);

        $this->assertSame(
            'custom',
            $context->guard()
        );

        $this->assertSame(
            'custom_provider',
            $context->provider()
        );

        $this->assertSame(
            TestUser::class,
            $context->userModel()
        );
    }
}