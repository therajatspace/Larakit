<?php

namespace Therajatspace\Larakit\Tests\Unit\Auth\Password;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Session\Store;
use Illuminate\Validation\ValidationException;
use Mockery;
use Orchestra\Testbench\TestCase;
use Therajatspace\Larakit\Auth\Password\PasswordConfirmationService;

class PasswordConfirmationServiceTest extends TestCase
{
    public function test_correct_password_confirms_session(): void
    {
        $user = Mockery::mock(
            Authenticatable::class
        );

        $provider = Mockery::mock(
            UserProvider::class
        );

        $provider
            ->shouldReceive('validateCredentials')
            ->once()
            ->with(
                $user,
                ['password' => 'secret']
            )
            ->andReturnTrue();

        $guard = Mockery::mock(
            StatefulGuard::class
        );

        $guard
            ->shouldReceive('check')
            ->once()
            ->andReturnTrue();

        $guard
            ->shouldReceive('user')
            ->once()
            ->andReturn($user);

        $guard
            ->shouldReceive('getProvider')
            ->once()
            ->andReturn($provider);

        $session = new Store(
            'testing',
            $this->app['session.store']->getHandler()
        );

        $request = Request::create('/');

        $request->setLaravelSession($session);

        $service = new PasswordConfirmationService(
            $guard,
            $request
        );

        $service->confirm('secret');

        $this->assertSame(
            time(),
            $request->session()->get(
                'auth.password_confirmed_at'
            )
        );
    }

    public function test_incorrect_password_is_rejected(): void
    {
        $user = Mockery::mock(
            Authenticatable::class
        );

        $provider = Mockery::mock(
            UserProvider::class
        );

        $provider
            ->shouldReceive('validateCredentials')
            ->once()
            ->with(
                $user,
                ['password' => 'wrong']
            )
            ->andReturnFalse();

        $guard = Mockery::mock(
            StatefulGuard::class
        );

        $guard
            ->shouldReceive('check')
            ->once()
            ->andReturnTrue();

        $guard
            ->shouldReceive('user')
            ->once()
            ->andReturn($user);

        $guard
            ->shouldReceive('getProvider')
            ->once()
            ->andReturn($provider);

        $request = Request::create('/');

        $service = new PasswordConfirmationService(
            $guard,
            $request
        );

        $this->expectException(
            ValidationException::class
        );

        $service->confirm('wrong');
    }

    public function test_unauthenticated_user_is_rejected(): void
    {
        $guard = Mockery::mock(
            StatefulGuard::class
        );

        $guard
            ->shouldReceive('check')
            ->once()
            ->andReturnFalse();

        $request = Request::create('/');

        $service = new PasswordConfirmationService(
            $guard,
            $request
        );

        $this->expectException(
            \RuntimeException::class
        );

        $service->confirm('secret');
    }

    public function test_missing_authenticated_user_is_rejected(): void
    {
        $guard = Mockery::mock(
            StatefulGuard::class
        );

        $guard
            ->shouldReceive('check')
            ->once()
            ->andReturnTrue();

        $guard
            ->shouldReceive('user')
            ->once()
            ->andReturnNull();

        $request = Request::create('/');

        $service = new PasswordConfirmationService(
            $guard,
            $request
        );

        $this->expectException(
            \RuntimeException::class
        );

        $service->confirm('secret');
    }
}
