<?php

namespace Therajatspace\Larakit\Auth\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Therajatspace\Larakit\Auth\Contracts\PasswordResetServiceContract;
use Therajatspace\Larakit\Auth\Password\ResetPasswordData;

class ResetPasswordController
{
    public function __construct(
        protected PasswordResetServiceContract $passwordReset
    ) {
    }

    public function store(
        Request $request
    ): JsonResponse {
        $data = ResetPasswordData::make(
            token: (string) $request->input('token', ''),
            email: (string) $request->input('email', ''),
            password: (string) $request->input('password', ''),
            passwordConfirmation: (string) $request->input(
                'password_confirmation',
                ''
            ),
        );

        $status = $this->passwordReset->reset($data);

        if ($status !== Password::PasswordReset) {
            return response()->json([
                'message' => __(
                    'passwords.' . $status
                ),
            ], 422);
        }

        return response()->json([
            'message' => __(
                'passwords.' . $status
            ),
        ]);
    }
}
