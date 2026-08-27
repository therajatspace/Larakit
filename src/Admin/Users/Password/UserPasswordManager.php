<?php

namespace Therajatspace\Larakit\Admin\Users\Password;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
use Therajatspace\Larakit\Admin\Users\Contracts\UserPasswordManagerContract;

class UserPasswordManager implements UserPasswordManagerContract
{
    public function setPassword(
        Authenticatable $user,
        string $password,
        ?string $passwordConfirmation = null
    ): void {
        $this->ensureSupportedUser($user);

        $passwordConfirmation ??= $password;

        $this->validatePassword(
            $password,
            $passwordConfirmation
        );

        $user->forceFill([
            'password' => Hash::make($password),
            'remember_token' => Str::random(60),
        ])->save();
    }

    protected function validatePassword(
        string $password,
        string $passwordConfirmation
    ): void {
        Validator::make(
            [
                'password' => $password,
                'password_confirmation' => $passwordConfirmation,
            ],
            [
                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    PasswordRule::defaults(),
                ],
            ]
        )->validate();
    }

    protected function ensureSupportedUser(
        Authenticatable $user
    ): void {
        if (!$user instanceof Model) {
            throw new InvalidArgumentException(
                'The configured user model must extend Illuminate\Database\Eloquent\Model.'
            );
        }

        if (!array_key_exists(
            'password',
            $user->getAttributes()
        ) && !array_key_exists(
            'password',
            $user->getCasts()
        )) {
            throw new InvalidArgumentException(
                'The configured user model must support a password attribute.'
            );
        }
    }
}