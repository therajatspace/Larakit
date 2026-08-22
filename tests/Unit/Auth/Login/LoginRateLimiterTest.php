<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Login;

use Mockery;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Contracts\AuthContextContract;
use Therajatspace\Larakit\Auth\Contracts\LoginRateLimiterContract;
use Therajatspace\Larakit\Auth\Login\LoginRateLimiter;
use Therajatspace\Larakit\LaraKitServiceProvider;

class LoginRateLimiterTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
            AuthServiceProvider::class,
        ];
    }

    public function test_rate_limiter_can_be_resolved(): void
    {
        $this->assertInstanceOf(
            LoginRateLimiter::class,
            $this->app->make(LoginRateLimiter::class)
        );
    }

    public function test_rate_limiter_contract_can_be_resolved(): void
    {
        $this->assertInstanceOf(
            LoginRateLimiter::class,
            $this->app->make(
                LoginRateLimiterContract::class
            )
        );
    }

    public function test_rate_limit_is_enabled_by_default(): void
    {
        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertTrue(
            $limiter->enabled()
        );
    }

    public function test_rate_limit_can_be_disabled(): void
    {
        config()->set(
            'larakit.auth.rate_limit.enabled',
            false
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertFalse(
            $limiter->enabled()
        );
    }

    public function test_default_account_max_attempts_is_five(): void
    {
        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            5,
            $limiter->accountMaxAttempts()
        );

        $this->assertSame(
            5,
            $limiter->maxAttempts()
        );
    }

    public function test_default_ip_max_attempts_is_thirty(): void
    {
        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            30,
            $limiter->ipMaxAttempts()
        );
    }

    public function test_default_account_decay_is_sixty_seconds(): void
    {
        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            60,
            $limiter->accountDecaySeconds()
        );

        $this->assertSame(
            60,
            $limiter->decaySeconds()
        );
    }

    public function test_default_ip_decay_is_sixty_seconds(): void
    {
        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            60,
            $limiter->ipDecaySeconds()
        );
    }

    public function test_custom_account_max_attempts_is_used(): void
    {
        config()->set(
            'larakit.auth.rate_limit.account.max_attempts',
            10
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            10,
            $limiter->accountMaxAttempts()
        );

        $this->assertSame(
            10,
            $limiter->maxAttempts()
        );
    }

    public function test_custom_ip_max_attempts_is_used(): void
    {
        config()->set(
            'larakit.auth.rate_limit.ip.max_attempts',
            50
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            50,
            $limiter->ipMaxAttempts()
        );
    }

    public function test_custom_account_decay_is_used(): void
    {
        config()->set(
            'larakit.auth.rate_limit.account.decay_seconds',
            120
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            120,
            $limiter->accountDecaySeconds()
        );

        $this->assertSame(
            120,
            $limiter->decaySeconds()
        );
    }

    public function test_custom_ip_decay_is_used(): void
    {
        config()->set(
            'larakit.auth.rate_limit.ip.decay_seconds',
            300
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            300,
            $limiter->ipDecaySeconds()
        );
    }

    public function test_account_and_ip_decay_can_be_configured_independently(): void
    {
        config()->set(
            'larakit.auth.rate_limit.account.decay_seconds',
            120
        );

        config()->set(
            'larakit.auth.rate_limit.ip.decay_seconds',
            300
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            120,
            $limiter->accountDecaySeconds()
        );

        $this->assertSame(
            300,
            $limiter->ipDecaySeconds()
        );
    }

    public function test_attempt_limits_are_never_less_than_one(): void
    {
        config()->set(
            'larakit.auth.rate_limit.account.max_attempts',
            0
        );

        config()->set(
            'larakit.auth.rate_limit.ip.max_attempts',
            -10
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            1,
            $limiter->accountMaxAttempts()
        );

        $this->assertSame(
            1,
            $limiter->ipMaxAttempts()
        );
    }

    public function test_decay_values_are_never_less_than_one(): void
    {
        config()->set(
            'larakit.auth.rate_limit.account.decay_seconds',
            0
        );

        config()->set(
            'larakit.auth.rate_limit.ip.decay_seconds',
            -10
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $this->assertSame(
            1,
            $limiter->accountDecaySeconds()
        );

        $this->assertSame(
            1,
            $limiter->ipDecaySeconds()
        );
    }

    public function test_account_attempts_are_tracked(): void
    {
        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->andReturn('web');

        $this->app->instance(
            AuthContextContract::class,
            $context
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $limiter->hit(
            'john@example.com'
        );

        $this->assertSame(
            1,
            $limiter->attempts(
                'john@example.com'
            )
        );
    }

    public function test_different_accounts_have_separate_account_counters(): void
    {
        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->andReturn('web');

        $this->app->instance(
            AuthContextContract::class,
            $context
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $limiter->hit(
            'john@example.com'
        );

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

    public function test_clearing_account_attempts_does_not_clear_ip_attempts(): void
    {
        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->andReturn('web');

        $this->app->instance(
            AuthContextContract::class,
            $context
        );

        $limiter = $this->app->make(
            LoginRateLimiter::class
        );

        $limiter->hit(
            'john@example.com'
        );

        $this->assertSame(
            1,
            $limiter->attempts(
                'john@example.com'
            )
        );

        $this->assertSame(
            1,
            $limiter->ipAttempts()
        );

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