<?php

namespace Therajatspace\Larakit\Auth\Login;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Therajatspace\Larakit\Auth\Contracts\AuthContextContract;
use Therajatspace\Larakit\Auth\Contracts\LoginServiceContract;
use Therajatspace\Larakit\Auth\Contracts\LoginRateLimiterContract;

class LoginService implements LoginServiceContract
{
    public function __construct(
        protected AuthContextContract $context,
        protected LoginValidator $validator,
        protected AuthFactory $auth,
        protected LoginRateLimiterContract $rateLimiter,
    ) {
    }

    /**
     * Authenticate a user using the application's configured guard.
     *
     * @throws AuthenticationException
     */
    public function login(
        LoginData $data
    ): Authenticatable {
        $normalizedEmail = $this->normalizeEmail(
            $data->email
        );

        $normalizedData = LoginData::make(
            email: $normalizedEmail,
            password: $data->password,
            remember: $data->remember,
        );

        $this->validator->validate(
            $normalizedData
        );

        if (
            $this->rateLimiter->tooManyAttempts(
                $normalizedData->email
            )
        ) {
            throw new AuthenticationException(
                'Too many login attempts. Please try again later.'
            );
        }

        $guard = $this->guard();

        $credentials = [
            'email' => $normalizedData->email,
            'password' => $normalizedData->password,
        ];

        if (
            !$guard->attempt(
                $credentials,
                $normalizedData->remember
            )
        ) {
            $this->rateLimiter->hit(
                $normalizedData->email
            );

            throw new AuthenticationException(
                'The provided credentials are incorrect.'
            );
        }

        $this->rateLimiter->clear(
            $normalizedData->email
        );

        Session::regenerate();

        /** @var Authenticatable $user */
        $user = $guard->user();

        return $user;
    }

    public function logout(): void
    {
        $guard = $this->guard();

        $guard->logout();

        Session::invalidate();

        Session::regenerateToken();
    }

    protected function guard(): StatefulGuard
    {
        $guard = $this->auth->guard(
            $this->context->guard()
        );

        if (!$guard instanceof StatefulGuard) {
            throw new AuthenticationException(
                'The configured authentication guard does not support session authentication.'
            );
        }

        return $guard;
    }

    protected function normalizeEmail(
        string $email
    ): string {
        return mb_strtolower(
            trim($email)
        );
    }
}