<?php

namespace Therajatspace\Larakit\Auth\Password;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class PasswordResetValidator
{
    /**
     * @throws ValidationException
     */
    public function validateForgot(
        ForgotPasswordData $data
    ): void {
        Validator::make(
            [
                'email' => $data->email,
            ],
            [
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                ],
            ]
        )->validate();
    }

    /**
     * @throws ValidationException
     */
    public function validateReset(
        ResetPasswordData $data
    ): void {
        Validator::make(
            [
                'token' => $data->token,
                'email' => $data->email,
                'password' => $data->password,
                'password_confirmation' => $data->passwordConfirmation,
            ],
            [
                'token' => [
                    'required',
                    'string',
                ],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                ],

                'password' => [
                    'required',
                    'string',
                    'confirmed',
                    Password::defaults(),
                ],
            ]
        )->validate();
    }
}
