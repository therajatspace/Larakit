<?php

namespace Therajatspace\Larakit\Auth\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Therajatspace\Larakit\Auth\Contracts\PasswordResetServiceContract;
use Therajatspace\Larakit\Auth\Password\ForgotPasswordData;

class ForgotPasswordController
{
    public function __construct(
        protected PasswordResetServiceContract $passwordReset
    ) {
    }

    public function store(
        Request $request
    ): JsonResponse {
        $data = ForgotPasswordData::make(
            (string) $request->input('email', '')
        );

        $status = $this->passwordReset->sendResetLink(
            $data
        );

        if ($status !== Password::ResetLinkSent) {
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
