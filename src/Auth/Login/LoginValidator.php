<?php

namespace Therajatspace\Larakit\Auth\Login;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class LoginValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(LoginData $data): void
    {
        Validator::make(
            [
                'email' => $data->email,
                'password' => $data->password,
            ],
            [
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                ],

                'password' => [
                    'required',
                    'string',
                ],

                'remember' => [
                    'boolean',
                ],
            ]
        )->validate();
    }
}