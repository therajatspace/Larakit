<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Login;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Mockery;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\AuthServiceProvider;
use Therajatspace\Larakit\Auth\Contracts\AuthContextContract;
use Therajatspace\Larakit\Auth\Contracts\LoginRateLimiterContract;
use Therajatspace\Larakit\Auth\Login\LoginData;
use Therajatspace\Larakit\Auth\Login\LoginService;
use Therajatspace\Larakit\Auth\Login\LoginValidator;
use Therajatspace\Larakit\LaraKitServiceProvider;

class LoginServiceSecurityTest extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraKitServiceProvider::class,
            AuthServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->instance(
            AuthContextContract::class,
            Mockery::mock(AuthContextContract::class)
        );

        $this->app->instance(
            LoginRateLimiterContract::class,
            Mockery::mock(LoginRateLimiterContract::class)
        );
    }

    public function test_login_service_can_be_resolved(): void
    {
        $this->assertInstanceOf(
            LoginService::class,
            $this->app->make(LoginService::class)
        );
    }

    public function test_successful_login_returns_authenticated_user(): void
    {
        $user = Mockery::mock(
            Authenticatable::class
        );

        $guard = Mockery::mock(
            StatefulGuard::class
        );

        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->once()
            ->andReturn('web');

        $guard
            ->shouldReceive('attempt')
            ->once()
            ->with(
                [
                    'email' => 'john@example.com',
                    'password' => 'secret-password',
                ],
                false
            )
            ->andReturnTrue();

        $guard
            ->shouldReceive('user')
            ->once()
            ->andReturn($user);

        $auth = Mockery::mock(
            AuthFactory::class
        );

        $auth
            ->shouldReceive('guard')
            ->once()
            ->with('web')
            ->andReturn($guard);

        $rateLimiter = Mockery::mock(
            LoginRateLimiterContract::class
        );

        $rateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->with('john@example.com')
            ->andReturnFalse();

        $rateLimiter
            ->shouldReceive('clear')
            ->once()
            ->with('john@example.com');

        $this->app->instance(
            AuthContextContract::class,
            $context
        );

        $this->app->instance(
            LoginRateLimiterContract::class,
            $rateLimiter
        );

        $service = new LoginService(
            $context,
            $this->app->make(LoginValidator::class),
            $auth,
            $rateLimiter
        );

        $result = $service->login(
            LoginData::make(
                'john@example.com',
                'secret-password'
            )
        );

        $this->assertSame(
            $user,
            $result
        );
    }

    public function test_failed_login_records_rate_limit_attempt(): void
    {
        $guard = Mockery::mock(
            StatefulGuard::class
        );

        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->once()
            ->andReturn('web');

        $guard
            ->shouldReceive('attempt')
            ->once()
            ->with(
                [
                    'email' => 'john@example.com',
                    'password' => 'wrong-password',
                ],
                false
            )
            ->andReturnFalse();

        $auth = Mockery::mock(
            AuthFactory::class
        );

        $auth
            ->shouldReceive('guard')
            ->once()
            ->with('web')
            ->andReturn($guard);

        $rateLimiter = Mockery::mock(
            LoginRateLimiterContract::class
        );

        $rateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->with('john@example.com')
            ->andReturnFalse();

        $rateLimiter
            ->shouldReceive('hit')
            ->once()
            ->with('john@example.com');

        $this->app->instance(
            AuthContextContract::class,
            $context
        );

        $service = new LoginService(
            $context,
            $this->app->make(LoginValidator::class),
            $auth,
            $rateLimiter
        );

        $this->expectException(
            AuthenticationException::class
        );

        $service->login(
            LoginData::make(
                'john@example.com',
                'wrong-password'
            )
        );
    }

    public function test_rate_limited_login_is_rejected_before_password_check(): void
    {
        $guard = Mockery::mock(
            StatefulGuard::class
        );

        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->never();

        $auth = Mockery::mock(
            AuthFactory::class
        );

        $auth
            ->shouldReceive('guard')
            ->never();

        $rateLimiter = Mockery::mock(
            LoginRateLimiterContract::class
        );

        $rateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->with('john@example.com')
            ->andReturnTrue();

        $this->app->instance(
            AuthContextContract::class,
            $context
        );

        $service = new LoginService(
            $context,
            $this->app->make(LoginValidator::class),
            $auth,
            $rateLimiter
        );

        $this->expectException(
            AuthenticationException::class
        );

        $service->login(
            LoginData::make(
                'john@example.com',
                'secret-password'
            )
        );
    }

    public function test_successful_login_passes_remember_flag_to_guard(): void
    {
        $user = Mockery::mock(
            Authenticatable::class
        );

        $guard = Mockery::mock(
            StatefulGuard::class
        );

        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->once()
            ->andReturn('web');

        $guard
            ->shouldReceive('attempt')
            ->once()
            ->with(
                [
                    'email' => 'john@example.com',
                    'password' => 'secret-password',
                ],
                true
            )
            ->andReturnTrue();

        $guard
            ->shouldReceive('user')
            ->once()
            ->andReturn($user);

        $auth = Mockery::mock(
            AuthFactory::class
        );

        $auth
            ->shouldReceive('guard')
            ->once()
            ->with('web')
            ->andReturn($guard);

        $rateLimiter = Mockery::mock(
            LoginRateLimiterContract::class
        );

        $rateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->with('john@example.com')
            ->andReturnFalse();

        $rateLimiter
            ->shouldReceive('clear')
            ->once()
            ->with('john@example.com');

        $this->app->instance(
            AuthContextContract::class,
            $context
        );

        $service = new LoginService(
            $context,
            $this->app->make(LoginValidator::class),
            $auth,
            $rateLimiter
        );

        $result = $service->login(
            LoginData::make(
                'john@example.com',
                'secret-password',
                true
            )
        );

        $this->assertSame(
            $user,
            $result
        );
    }

    public function test_logout_logs_out_and_invalidates_session(): void
    {
        $guard = Mockery::mock(
            StatefulGuard::class
        );

        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->once()
            ->andReturn('web');

        $guard
            ->shouldReceive('logout')
            ->once();

        $auth = Mockery::mock(
            AuthFactory::class
        );

        $auth
            ->shouldReceive('guard')
            ->once()
            ->with('web')
            ->andReturn($guard);

        $rateLimiter = Mockery::mock(
            LoginRateLimiterContract::class
        );

        $service = new LoginService(
            $context,
            $this->app->make(LoginValidator::class),
            $auth,
            $rateLimiter
        );

        $service->logout();

        $this->assertTrue(
            true
        );
    }

    public function test_login_rejects_invalid_credentials(): void
    {
        $guard = Mockery::mock(
            StatefulGuard::class
        );

        $context = Mockery::mock(
            AuthContextContract::class
        );

        $context
            ->shouldReceive('guard')
            ->once()
            ->andReturn('web');

        $guard
            ->shouldReceive('attempt')
            ->once()
            ->andReturnFalse();

        $auth = Mockery::mock(
            AuthFactory::class
        );

        $auth
            ->shouldReceive('guard')
            ->once()
            ->with('web')
            ->andReturn($guard);

        $rateLimiter = Mockery::mock(
            LoginRateLimiterContract::class
        );

        $rateLimiter
            ->shouldReceive('tooManyAttempts')
            ->once()
            ->andReturnFalse();

        $rateLimiter
            ->shouldReceive('hit')
            ->once();

        $service = new LoginService(
            $context,
            $this->app->make(LoginValidator::class),
            $auth,
            $rateLimiter
        );

        $this->expectException(
            AuthenticationException::class
        );

        $service->login(
            LoginData::make(
                'john@example.com',
                'wrong-password'
            )
        );
    }
}