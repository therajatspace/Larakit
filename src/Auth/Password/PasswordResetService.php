<?php

namespace Therajatspace\Larakit\Auth\Password;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use RuntimeException;
use Therajatspace\Larakit\Auth\Contracts\PasswordResetServiceContract;

class PasswordResetService implements PasswordResetServiceContract
{
    public function __construct(
        protected PasswordResetValidator $validator,
    ) {
    }

    public function sendResetLink(
        ForgotPasswordData $data
    ): string {
        $this->ensureEnabled();

        $this->validator->validateForgot($data);

        return $this->broker()->sendResetLink([
            'email' => $data->email,
        ]);
    }

    public function reset(
        ResetPasswordData $data
    ): string {
        $this->ensureEnabled();

        $this->validator->validateReset($data);

        return $this->broker()->reset(
            [
                'token' => $data->token,
                'email' => $data->email,
                'password' => $data->password,
                'password_confirmation' => $data->passwordConfirmation,
            ],
            function ($user, $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(
                    new PasswordReset($user)
                );
            }
        );
    }

    protected function broker(): PasswordBroker
    {
        $broker = config(
            'larakit.auth.password_reset.broker'
        );

        if (
            $broker !== null &&
            (! is_string($broker) || trim($broker) === '')
        ) {
            throw new RuntimeException(
                'LaraKit password reset broker must be a valid string or null.'
            );
        }

        return $broker !== null
            ? Password::broker($broker)
            : Password::broker();
    }

    protected function ensureEnabled(): void
    {
        if (! config(
            'larakit.auth.password_reset.enabled',
            true
        )) {
            throw new RuntimeException(
                'LaraKit password reset is disabled.'
            );
        }
    }
}