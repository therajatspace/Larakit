<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Login;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Mockery;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Contracts\AuthContextContract;
use Therajatspace\Larakit\Auth\Contracts\LoginRateLimiterContract;
use Therajatspace\Larakit\Auth\Login\LoginRateLimiter;
use Therajatspace\Larakit\LaraKitServiceProvider;

class LoginRateLimiterSecurityTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
            AuthServiceProvider::class,
        ];
    }

    public function test_ip_attempts_are_tracked(): void
    {
        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->andReturn('web');

        $request = Request::create(
            '/login',
            'POST',
            [],
            [],
            [],
            [
                'REMOTE_ADDR' => '192.0.2.10',
            ]
        );

        $this->app->instance(
            AuthContextContract::class,
            $context
        );

        $this->app->instance(
            Request::class,
            $request
        );

        $limiter = $this->app->make(
            LoginRateLimiterContract::class
        );

        $limiter->hit('john@example.com');
        $limiter->hit('jane@example.com');

        $this->assertSame(
            2,
            $limiter->ipAttempts()
        );
    }

    public function test_account_attempts_are_separate_for_different_emails(): void
    {
        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->andReturn('web');

        $request = Request::create(
            '/login',
            'POST',
            [],
            [],
            [],
            [
                'REMOTE_ADDR' => '192.0.2.10',
            ]
        );

        $this->app->instance(
            AuthContextContract::class,
            $context
        );

        $this->app->instance(
            Request::class,
            $request
        );

        $limiter = $this->app->make(
            LoginRateLimiterContract::class
        );

        $limiter->hit('john@example.com');

        $this->assertSame(
            1,
            $limiter->attempts(
                'john@example.com'
            )
        );

        $this->assertSame(
            0,
            $limiter->attempts(
                'jane@example.com'
            )
        );
    }

    public function test_successful_account_clear_does_not_clear_ip_bucket(): void
    {
        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->andReturn('web');

        $request = Request::create(
            '/login',
            'POST',
            [],
            [],
            [],
            [
                'REMOTE_ADDR' => '192.0.2.10',
            ]
        );

        $this->app->instance(
            AuthContextContract::class,
            $context
        );

        $this->app->instance(
            Request::class,
            $request
        );

        $limiter = $this->app->make(
            LoginRateLimiterContract::class
        );

        $limiter->hit('john@example.com');

        $limiter->clear(
            'john@example.com'
        );

        $this->assertSame(
            0,
            $limiter->attempts(
                'john@example.com'
            )
        );

        $this->assertSame(
            1,
            $limiter->ipAttempts()
        );
    }
}