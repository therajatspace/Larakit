<?php

namespace Therajatspace\Larakit\Auth\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Therajatspace\Larakit\Auth\Contracts\PasswordConfirmationServiceContract;

class PasswordConfirmationController
{
    public function __construct(
        protected PasswordConfirmationServiceContract $passwordConfirmation
    ) {
    }

    public function store(
        Request $request
    ): JsonResponse {
        $request->validate([
            'password' => [
                'required',
                'string',
            ],
        ]);

        $this->passwordConfirmation->confirm(
            (string) $request->input('password')
        );

        return response()->json([
            'message' => 'Password confirmed.',
        ]);
    }
}
