<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Users;

use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\Users\UserResolver;
use Therajatspace\Larakit\LaraKitServiceProvider;
use Therajatspace\Larakit\Tests\Fixtures\TestUser;

class UserResolverTest extends TestCase
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

        config()->set('auth.defaults.guard', 'web');

        config()->set('auth.guards.web', [
            'driver' => 'session',
            'provider' => 'users',
        ]);

        config()->set('auth.providers.users', [
            'driver' => 'eloquent',
            'model' => TestUser::class,
        ]);

        config()->set(
            'larakit.auth.guard',
            null
        );

        config()->set(
            'larakit.auth.user.model',
            null
        );
    }

    public function test_it_resolves_the_default_guard(): void
    {
        $resolver = $this->app->make(UserResolver::class);

        $this->assertSame(
            'web',
            $resolver->guard()
        );
    }

    public function test_it_resolves_the_guard_provider(): void
    {
        $resolver = $this->app->make(UserResolver::class);

        $this->assertSame(
            'users',
            $resolver->provider()
        );
    }

    public function test_it_resolves_the_user_model_from_laravel_configuration(): void
    {
        $resolver = $this->app->make(UserResolver::class);

        $this->assertSame(
            TestUser::class,
            $resolver->model()
        );
    }

    public function test_larakit_guard_takes_priority_over_laravel_default_guard(): void
    {
        config()->set(
            'larakit.auth.guard',
            'custom'
        );

        config()->set(
            'auth.guards.custom',
            [
                'driver' => 'session',
                'provider' => 'users',
            ]
        );

        $resolver = $this->app->make(UserResolver::class);

        $this->assertSame(
            'custom',
            $resolver->guard()
        );
    }

    public function test_larakit_model_takes_priority_over_laravel_model(): void
    {
        config()->set(
            'larakit.auth.user.model',
            TestUser::class
        );

        config()->set(
            'auth.providers.users.model',
            'Some\\Other\\User'
        );

        $resolver = $this->app->make(UserResolver::class);

        $this->assertSame(
            TestUser::class,
            $resolver->model()
        );
    }

    public function test_non_existing_model_is_rejected(): void
    {
        config()->set(
            'larakit.auth.user.model',
            'Some\\NonExisting\\User'
        );

        $resolver = $this->app->make(UserResolver::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $resolver->model();
    }

    public function test_non_authenticatable_model_is_rejected(): void
    {
        config()->set(
            'larakit.auth.user.model',
            \stdClass::class
        );

        $resolver = $this->app->make(UserResolver::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $resolver->model();
    }

    public function test_missing_guard_is_rejected(): void
    {
        config()->set(
            'larakit.auth.guard',
            null
        );

        config()->set(
            'auth.defaults.guard',
            null
        );

        $resolver = $this->app->make(UserResolver::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $resolver->guard();
    }

    public function test_missing_provider_is_rejected(): void
    {
        config()->set(
            'auth.guards.web',
            [
                'driver' => 'session',
            ]
        );

        $resolver = $this->app->make(UserResolver::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $resolver->provider();
    }

    public function test_missing_user_model_is_rejected(): void
    {
        config()->set(
            'auth.providers.users.model',
            null
        );

        $resolver = $this->app->make(UserResolver::class);

        $this->expectException(
            InvalidArgumentException::class
        );

        $resolver->model();
    }
}